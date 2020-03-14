<?php

namespace Opake\Model;

class Condition extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'condition';
	protected $_row = [
		'id' => null,
		'code' => '',
		'desc' => '',
	];

	public function getList()
	{
		return $this->order_by('code')->find_all();
	}

}
