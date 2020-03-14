<?php

namespace Opake\Model;

class PlaceService extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'place_service';
	protected $_row = [
		'id' => null,
		'code' => '',
		'name' => '',
		'desc' => ''
	];

	public function getList()
	{
		return $this->order_by($this->pixie->db->expr('ABS(code)'))->find_all();
	}

}
