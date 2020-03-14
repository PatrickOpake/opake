<?php

namespace OpakeAdmin\Controller\Vendors;

use Opake\Model\Vendor;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionList()
	{
		$service = $this->services->get('vendors');
		$model = $service->getItem()->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Vendor($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $item) {
			$items[] = $item->toShortArray();
		}
		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionVendor()
	{
		$model = $this->loadModel('Vendor', 'subid');
		$this->result = $model->toArray();
	}

	public function actionContacts()
	{
		$vendor = $this->loadModel('Vendor', 'subid');
		$items = [];
		foreach ($vendor->contacts->find_all() as $item) {
			$items[] = $item->toArray();
		}
		$this->result = $items;
	}

	public function actionSearch()
	{
		$result = [];
		$model = $this->services->get('vendors')->getItem();
		$model->where('organization_id', $this->org->id);

		$query = $this->request->get('query');
		$type = $this->request->get('type');

		if ($type === Vendor::TYPE_DIST) {
			$model->where('is_dist', 1);
		} elseif ($type === Vendor::TYPE_MANF) {
			$model->where('is_manf', 1);
		}

		if ($query) {
			$model->where('name', 'like', '%' . $query . '%');
		}
		$model->limit(12);

		foreach ($model->find_all() as $item) {
			$result[] = $item->toShortArray();
		}
		$this->result = $result;
	}

	public function actionSave()
	{
		$data = $this->getData();
		if ($data) {

			$model = $this->orm->get('Vendor', isset($data->id) ? $data->id : null);

			if (!$model->loaded()) {
				$model->organization_id = $this->org->id;
			} elseif ($model->organization_id !== $this->org->id) {
				throw new \Opake\Exception\Ajax('Vendor doesn\'t exist');
			}
			$model->fill($data);

			$model->beginTransaction();
			try {

				$this->updateModel($model, $data, true);

				$model->contacts->delete_all();

				if (!empty($data->contacts)) {
					foreach ($data->contacts as $contactData) {
						$contactModel = $this->orm->get('Vendor_Contact', isset($contactData->id) ? $contactData->id : null);
						$contactModel->vendor_id = $model->id;
						$this->updateModel($contactModel, $contactData);
					}
				}

			} catch (\Exception $e) {
				$this->logSystemError($e);
				$model->rollback();
				throw new \Opake\Exception\Ajax($e->getMessage());
			}
			$model->commit();

			$this->result = ['id' => (int)$model->id];
		}
	}

}
