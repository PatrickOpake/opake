<?php

namespace OpakeAdmin\Controller\Master;

use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Inventory as InventoryModel;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$service = $this->services->get('Inventory');
		$model = $service->getItem()->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Inventory($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $item) {
			$items[] = $item->toArray();
		}
		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionSearchMasterItems()
	{
		$result = [];
		$model = $this->services->get('Inventory')->getItem();
		$model->where('organization_id', $this->org->id);

		$query = $this->request->get('query');

		if ($query) {
			$model->where([
				['name', 'like', '%' . $query . '%'],

			]);
		}
		$model->limit(12);

		foreach ($model->find_all() as $item) {
			$result[] = $item->toShortArray();
		}
		$this->result = $result;
	}

	public function actionSave()
	{
		$service = $this->services->get('Inventory');
		$items = $this->getData();

		if ($items) {
			$service->beginTransaction();
			foreach ($items as $item) {
				$model = $this->orm->get('Inventory', isset($item->id) ? $item->id : null);
				if (!$model->loaded()) {
					$model->organization_id = $this->org->id;
				} elseif ($model->organization_id !== $this->org->id) {
					throw new \Opake\Exception\Ajax('Item Master doesn\'t exist');
				}

				try {
					if ($item) {
						$model->fill($item);
					}

					$this->checkValidationErrors($model);
					$model->save();

					$this->updateRelatedInventoryData($model, $item);

				} catch (\Exception $e) {
					$this->logSystemError($e);
					$service->rollback();
					throw new \Opake\Exception\Ajax($e->getMessage());
				}
			}
			$service->commit();
			$this->result = ['result' => 'ok'];
		}
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
	}

}
