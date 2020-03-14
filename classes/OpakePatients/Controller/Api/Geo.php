<?php

namespace OpakePAtients\Controller\Api;

use OpakePatients\Controller\AbstractAjax;

class Geo extends AbstractAjax
{
	public function actionCountries()
	{
		$model = $this->orm->get('Geo_Country');
		$this->result = [];

		foreach ($model->getList() as $item) {
			$this->result[] = $item->toArray();
		}
	}

	public function actionStates()
	{
		$model = $this->orm->get('Geo_State');
		$this->result = [];

		foreach ($model->getList() as $item) {
			$this->result[] = $item->toArray();
		}
	}

	public function actionCities()
	{
		$model = $this->orm->get('Geo_City');
		$this->result = [];

		foreach ($model->getList($this->request->get('state_id')) as $item) {
			$this->result[] = $item->toArray();
		}
	}
}
