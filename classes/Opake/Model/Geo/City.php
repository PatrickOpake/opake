<?php

namespace Opake\Model\Geo;

class City extends \Opake\Model\AbstractModel
{

	public $id_field = 'id';
	public $table = 'geo_city';
	protected $_row = [
		'id' => null,
		'state_id' => null,
		'name' => '',
		'organization_id' => null,
	];

	public function getList($stateId)
	{
		if ($stateId) {
			$this->where('state_id', $stateId);
		}
		$this->order_by('name', 'ASC');
		return $this->find_all();
	}

	public function addCustomRecord($organizationId, $stateId, $name)
	{
		$this->where('name', $name);
		$this->where('state_id', $stateId);
		$existedModel = $this->find();
		if ($existedModel->loaded()) {
			return $existedModel;
		}

		/** @var City $newModel */
		$newModel = $this->pixie->orm->get($this->model_name);
		$newModel->organization_id = $organizationId;
		$newModel->name = $name;
		$newModel->state_id = $stateId;

		$newModel->save();

		return $newModel;
	}

}
