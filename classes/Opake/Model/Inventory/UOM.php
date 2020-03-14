<?php

namespace Opake\Model\Inventory;

use Opake\Model\AbstractModel;

class UOM extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'inventory_uom';
	protected $_row = [
		'id' => null,
		'name' => '',
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('name')->rule('filled')->rule('min_length', 2)->error('Invalid Name');
		$validator->field('name')->rule('unique', $this)->error(sprintf('UOM with name %s already exists', $this->name));
		return $validator;
	}

	public function addCustomRecord($name)
	{
		$this->where('name', $name);
		$existedModel = $this->find();
		if ($existedModel->loaded()) {
			return $existedModel;
		}

		/** @var City $newModel */
		$newModel = $this->pixie->orm->get($this->model_name);
		$newModel->name = $name;

		$newModel->save();

		return $newModel;
	}
}
