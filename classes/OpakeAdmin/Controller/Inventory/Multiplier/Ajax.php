<?php

namespace OpakeAdmin\Controller\Inventory\Multiplier;

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

	public function actionList()
	{

		$model = $this->pixie->orm->get('Inventory_Multiplier');
		$model->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Inventory\Multiplier($this->pixie);
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

	public function actionSave()
	{
		$data = $this->getData();
		if ($data) {
			$model = $this->orm->get('Inventory_Multiplier', isset($data->id) ? $data->id : null);

			if (!$model->loaded()) {
				$model->organization_id = $this->org->id;
			} elseif ($model->organization_id !== $this->org->id) {
				throw new \Opake\Exception\Ajax('Inventory Multiplier doesn\'t exist');
			}

			$model->beginTransaction();
			try {
				if ($data) {
					$model->fill($data);
				}
				$this->checkValidationErrors($model);
				$model->save();
			} catch (\Exception $e) {
				$this->logSystemError($e);
				$model->rollback();
				throw new \Opake\Exception\Ajax($e->getMessage());
			}
			$model->commit();

			$this->result = ['id' => (int)$model->id];
		}
	}

	public function actionRemove()
	{
		$multiplier = $this->loadModel('Inventory_Multiplier', 'subid');
		$multiplier->delete();

		$this->result = 'ok';
	}
}
