<?php

namespace OpakeAdmin\Controller\Inventory;

use Opake\Model\Alert\Alert;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Inventory as InventoryModel;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionInventory()
	{
		$model = $this->loadModel('Inventory', 'subid');
		$this->result = $model->toArray();
	}

	public function actionTypes()
	{
		$search = new \OpakeAdmin\Model\Search\Inventory($this->pixie);

		$alert = $this->request->get('alert', null);
		$types = $search->getInventoryTypes($alert);

		$result = [];
		foreach ($types as $type) {
			$result[] = $type->name;
		}

		$this->result = $result;
	}

	public function actionAllTypes()
	{
		$model = $this->pixie->orm->get('Inventory_Type');
		$types = $model->order_by('name')->find_all();

		$result = [];
		foreach ($types as $type) {
			$result[] = $type->name;
		}

		$this->result = $result;
	}

	public function actionFullTypes()
	{
		$result = [];
		$types = $this->orm->get('Inventory_Type')->find_all()->as_array();
		foreach ($types as $type) {
			$result[] = $type->toArray();
		}
		$this->result = $result;
	}

	public function actionManufacturers()
	{
		$search = new \OpakeAdmin\Model\Search\Inventory($this->pixie);

		$alert = $this->request->get('alert', null);
		$query = $this->request->get('query', null);
		$manfs = $search->getManufacturers($query, $alert, $this->org->id());

		$result = [];
		foreach ($manfs as $manf) {
			$result[] = [
				'id' => (int) $manf->id(),
			    'name' => $manf->name
			];
		}

		$this->result = $result;
	}

	public function actionUoms()
	{
		$result = [];
		foreach ($this->orm->get('Inventory_UOM')->find_all() as $uom) {
			$result[] = $uom->toArray();
		}
		$this->result = $result;
	}

	public function actionSearchItems()
	{
		$result = [];
		$model = $this->services->get('Inventory')->getItem();
		$model->where('organization_id', $this->org->id);

		if ($this->request->get('type')) {
			$model->where('type', $this->request->get('type'));
		}

		$query = $this->request->get('query');
		$limit = $this->request->get('limit', 12);

		if ($query) {
			$model->where([
				['name', 'like', '%' . $query . '%'],
				['or', ['item_number', 'like', '%' . $query . '%']],
			]);
		}
		$model->limit($limit);

		foreach ($model->find_all() as $item) {
			$result[] = $item->toShortArray();
		}

		$this->result = $result;
	}

	public function actionVendors()
	{
		$result = [];

		$inventory = $this->loadModel('Inventory', 'subid');

		$item = $this->services->get('Vendors')->getItem();
		$item->query->join(['inventory_supply', 's'], [$item->table . '.id', 's.vendor_id'])
			->where('s.inventory_id', $inventory->id);

		$search = new \OpakeAdmin\Model\Search\Vendor($this->pixie);
		$results = $search->search($item, $this->request);

		foreach ($results as $item) {
			$result[] = $item->toShortArray();
		}
		$this->result = $result;
	}

	public function actionList()
	{
		$service = $this->services->get('inventory');
		$model = $service->getItem()->where('organization_id', $this->request->param('id'));

		$search = new \OpakeAdmin\Model\Search\Inventory($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $item) {
			$items[] = $item->toShortArray();
		}
		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];

		if ($this->request->get('alerts')) {
			$this->result['alerts'] = [
				Alert::TYPE_EXPIRING => $search->getCountByAlert($model, Alert::TYPE_EXPIRING),
				Alert::TYPE_LOW_INVENTORY => $search->getCountByAlert($model, Alert::TYPE_LOW_INVENTORY),
				Alert::TYPE_MISSING_INFO => $search->getCountByAlert($model, Alert::TYPE_MISSING_INFO),
			];
		}
	}

	public function actionSave()
	{
		$data = $this->getData();
		if ($data) {

			$model = $this->orm->get('Inventory', isset($data->id) ? $data->id : null);

			if (!$model->loaded()) {
				$model->organization_id = $this->org->id;
			} elseif ($model->organization_id !== $this->org->id) {
				throw new \Opake\Exception\Ajax('Inventory doesn\'t exist');
			}
			$imageModel = $model->getImageModel();

			$model->beginTransaction();
			try {

				if ($data) {
					$model->fill($data);
				}

				$this->checkValidationErrors($model);

				if (!$model->loaded()) {
					$model->origin = InventoryModel::ORIGIN_CUSTOM_RECORD;
				}

				$actionQueue = $this->pixie->activityLogger->newModelActionQueue($model);

				if (!$model->loaded()) {
					$actionQueue->addAction(ActivityRecord::ACTION_INVENTORY_ADD_ITEM);
				} else {
					$actionQueue->addAction(ActivityRecord::ACTION_INVENTORY_EDIT_ITEM);
				}

				$actionQueue->assign();

				$model->checkCompleteStatus();
				$model->save();

				$this->updateRelatedInventoryData($model, $data);

				$actionQueue->registerActions();

				if ($imageModel && !$model->image_id) {
					$imageModel->delete();
				}

			} catch (\Exception $e) {
				$this->logSystemError($e);
				$model->rollback();
				throw new \Opake\Exception\Ajax($e->getMessage());
			}
			$model->commit();

			$model->fire_events = true;
			$this->pixie->events->fireEvent('save.' . $model->table, $model);

			$this->result = ['id' => (int)$model->id];
		}
	}

	public function actionDelete()
	{
		$this->checkAccess('inventory', 'delete');

		$service = $this->services->get('inventory');

		$id = $this->request->param('subid');
		$inventory = $service->getItem($id);

		if (!$inventory->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}
		$service->delete($inventory);
	}

	protected function updateRelatedInventoryData($model, $data)
	{
		if (isset($data->manf)) {
			if ($data->manf->id) {
				$manufacturerModel = $this->orm->get('Vendor', isset($data->manf->id) ? $data->manf->id : null);
				$model->manufacturer = $manufacturerModel;
			} else {
				$manufacturerModel = $this->orm->get('Vendor');
				$manufacturerModel->fill($data->manf);
				$manufacturerModel->save();
				$model->manufacturer = $manufacturerModel;
			}
		}

		$this->updateInventoryPacks($model, $data);

		$model->supplies->delete_all();

		if (!empty($data->supplies)) {
			foreach ($data->supplies as $supplyData) {
				$supplyModel = $this->orm->get('Inventory_Supply', isset($supplyData->id) ? $supplyData->id : null);
				$supplyModel->inventory_id = $model->id;
				$supplyModel->vendor_id = $supplyData->vendor->id;
				$this->updateModel($supplyModel, $supplyData);
			}
		}

		$model->codes->delete_all();

		if (!empty($data->codes)) {
			foreach ($data->codes as $codeData) {
				$codeModel = $this->orm->get('Inventory_Code', isset($codeData->id) ? $codeData->id : null);
				$codeModel->inventory_id = $model->id;
				$this->updateModel($codeModel, $codeData);
			}
		}

		$model->kit->delete_all();

		if (!empty($data->kit_items)) {
			foreach ($data->kit_items as $kitData) {
				$kitModel = $this->orm->get('Inventory_Kit', isset($kitData->id) ? $kitData->id : null);
				$kitModel->inventory_id = $model->id;
				$this->updateModel($kitModel, $kitData);
			}
		}
	}

	protected function updateInventoryPacks($model, $data)
	{
		$isExistedModel = (isset($data->id));
		$oldModels = $model->packs->find_all();
		$actualModelIds = [];

		if (!empty($data->packs)) {
			foreach ($data->packs as $packData) {
				$packModel = $this->orm->get('Inventory_Pack', isset($packData->id) ? $packData->id : null);
				$packModel->inventory_id = $model->id;
				if ($packData) {
					$packModel->fill($packData);
				}

				$this->checkValidationErrors($packModel);

				$actionQueue = null;
				if ($isExistedModel) {
					$actionQueue = $this->pixie->activityLogger->newModelActionQueue($packModel);
					if ($packModel->loaded()) {
						$actionQueue->addAction(ActivityRecord::ACTION_INVENTORY_EDIT_QUANTITY_LOCATIONS);
					} else {
						$actionQueue->addAction(ActivityRecord::ACTION_INVENTORY_ADD_QUANTITY_LOCATIONS);
					}

					$actionQueue->assign();
				}

				$packModel->save();

				if ($actionQueue) {
					$actionQueue->registerActions();
				}

				$actualModelIds[] = $packModel->id();
			}
		}

		foreach ($oldModels as $oldModel) {
			if (!in_array($oldModel->id(), $actualModelIds)) {

				$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_INVENTORY_REMOVE_QUANTITY_LOCATIONS)
					->setModel($oldModel)
					->register();

				$oldModel->delete();
			}
		}
	}

}
