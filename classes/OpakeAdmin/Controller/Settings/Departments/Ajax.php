<?php

namespace OpakeAdmin\Controller\Settings\Departments;

use OpakeAdmin\Model\Search\Department as DepartmentSearch;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function actionIndex()
	{
		$items = [];
		$model = $this->orm->get('Department');

		$search = new DepartmentSearch($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toArray();
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
			$model = $this->orm->get('Department', isset($data->id) ? $data->id : null);

			$model->fill($data);

			$model->beginTransaction();
			try {
				$this->updateModel($model, $data, true);
			} catch (\Exception $e) {
				$this->logSystemError($e);
				$model->rollback();
				throw new \Opake\Exception\Ajax($e->getMessage());
			}
			$model->commit();

			$this->result = ['id' => (int) $model->id];
		}
	}

	public function actionActivate()
	{
		$model = $this->loadModel('Department', 'id');
		$model->active = 1;

		$model->save();
	}

	public function actionDeactivate()
	{
		$model = $this->loadModel('Department', 'id');
		$model->active = 0;

		$model->save();
	}

	public function actionDelete()
	{
		$model = $this->loadModel('Department', 'id');
		$model->delete();

		$model->save();
	}

	public function actionDepartment()
	{
		$model = $this->loadModel('Department', 'id');
		$this->result = $model->toArray();
	}
}
