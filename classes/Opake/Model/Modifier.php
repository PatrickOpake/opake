<?php

namespace Opake\Model;

class Modifier extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'modifier';
	protected $_row = [
		'id' => null,
		'code' => null,
		'description' => null
	];

	public function getList()
	{
		return $this->order_by('code')->find_all();
	}

}
