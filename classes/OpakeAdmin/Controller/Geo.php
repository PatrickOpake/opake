<?php

namespace OpakeAdmin\Controller;

class Geo extends Ajax
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
		$this->result[] = $model->toArray();

		foreach ($model->getList() as $item) {
			$this->result[] = $item->toArray();
		}
	}

	public function actionCities()
	{
		$stateId = $this->request->get('state_id');
		if ($stateId === null || $stateId === '') {
			throw new \Opake\Exception\BadRequest('Bad Request');
		}

		$model = $this->orm->get('Geo_City');
		$this->result = [];

		foreach ($model->getList($stateId) as $item) {
			$this->result[] = $item->toArray();
		}
	}

	public function actionZipcodes()
	{
		$model = $this->orm->get('Geo_ZipCode');
		$this->result = [];

		foreach ($model->getList($this->request->get('city_id')) as $item) {
			$this->result[] = $item->toArray();
		}
	}

}
