<?php

namespace Opake\Model;

class Discharge extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'discharge';
	protected $_row = [
		'id' => null,
		'code' => null,
		'desc' => null
	];

	public function getList()
	{
		return $this->order_by('code')->find_all();
	}

}
