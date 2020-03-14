<?php

namespace OpakePatients\Controller\Api;

use Opake\Exception\Forbidden;
use Opake\Helper\Pagination;
use OpakePatients\Controller\AbstractAjax;

class Documents extends AbstractAjax
{
	public function actionMyDocuments()
	{
		$user = $this->pixie->auth->user();
		if (!$user) {
			throw new Forbidden();
		}

		$patient = $user->patient;

		$model = $this->pixie->orm->get('Cases_Registration_Document')
			->with('file', 'case_registration');

		$model->query
			->join(['case_registration', 'cr'], [$model->table . '.case_registration_id', 'cr.id'], 'inner')
			->where('cr.patient_id', $patient->id())
			->where($model->table . '.uploaded_file_id', '<>', 'NULL')
			->order_by($model->table . '.uploaded_date', 'desc');


		$pagination = new Pagination();
		$pagination->setPage($this->request->get('p'));
		$pagination->setLimit($this->request->get('l'));

		$pagination->setCount($model->count_all());
		$results = $model->pagination($pagination)->find_all()->as_array();

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $pagination->getCount()
		];

	}
}
