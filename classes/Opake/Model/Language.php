<?php

namespace Opake\Model;

class Language extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'language';
	protected $_row = [
		'id' => null,
		'name' => '',
		'priority' => null
	];

	public function getList()
	{
		return $this->order_by('priority', 'desc')->order_by('name')->find_all();
	}

}
