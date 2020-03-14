<?php

namespace OpakeAdmin\Controller\Settings\PracticeGroups;

use Opake\Helper\Pagination;
use OpakeAdmin\Form\Settings\PracticeGroupForm;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function actionIndex()
	{
		$items = [];

		list($results, $pagination) = $this->searchGroupsByRequestParams();

		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $pagination->getCount()
		];
	}

	public function actionSave()
	{
		$data = $this->getData();
		if ($data) {
			$model = $this->orm->get('PracticeGroup', isset($data->id) ? $data->id : null);

			$form = new PracticeGroupForm($this->pixie, $model);
			$form->load($data);

			if ($form->isValid()) {

				$form->save();

				$this->result = [
					'success' => true,
					'id' => (int) $model->id()
				];

			} else {

				$this->result = [
					'success' => false,
					'errors' => $form->getCommonErrorList()
				];

			}
		}
	}

	public function actionActivate()
	{
		$model = $this->loadModel('PracticeGroup', 'id');
		$model->active = 1;

		$model->save();
	}

	public function actionDeactivate()
	{
		$model = $this->loadModel('PracticeGroup', 'id');
		$model->active = 0;

		$model->save();
	}

	public function actionDelete()
	{
		$model = $this->loadModel('PracticeGroup', 'id');
		$model->delete();

		$model->save();
	}

	protected function searchGroupsByRequestParams()
	{
		$request = $this->request;

		$pagination = new Pagination();
		$pagination->setPage($request->get('p'));
		$pagination->setLimit($request->get('l'));

		$model = $this->orm->get('PracticeGroup');

		$sort = $request->get('sort_by', 'name');
		$order = $request->get('sort_order', 'ASC');

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));

		switch ($sort) {
			case 'name':
				$model->order_by($model->table . '.name', $order);
				break;

			case 'active':
				$model->order_by($model->table . '.active', $order)
					->order_by($model->table . '.name', $order);
				break;
		}

		$results = $model->pagination($pagination)->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		$pagination->setCount($count);

		return [$results, $pagination];
	}
}
