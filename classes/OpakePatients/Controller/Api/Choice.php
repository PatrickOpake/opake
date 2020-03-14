<?php

namespace OpakePatients\Controller\Api;

use OpakePatients\Controller\AbstractAjax;

class Choice extends AbstractAjax
{
	public function actionLanguages()
	{
		$model = $this->orm->get('Language');
		$this->result = [];

		foreach ($model->getList() as $item) {
			$this->result[] = $item->toArray();
		}
	}

	public function actionInsurances()
	{
		$this->result = [];
		$q = $this->request->get('query');
		$insurance = $this->orm->get('Insurance_Payor');

		$insurance->where(['actual', 1]);

		if ($q !== null) {
			$insurance->where(['name', 'like', '%' . $q . '%']);
			$insurance->order_by('name', 'asc')->limit(12);

			foreach ($insurance->find_all() as $item) {
				$this->result[] = $item->toArray();
			}
		} else {
			foreach ($insurance->limit(12)->find_all() as $item) {
				$this->result[] = $item->toArray();
			}
		}
	}
}
