<?php

namespace Opake\Model\Geo;

class State extends \Opake\Model\AbstractModel
{

	public $id_field = 'id';
	public $table = 'geo_state';
	protected $_row = [
		'id' => null,
		'code' => '',
		'name' => ''
	];

	public function getList()
	{
		return $this->order_by('name')->find_all();
	}

}
