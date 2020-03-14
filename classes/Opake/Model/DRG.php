<?php

namespace Opake\Model;

class DRG extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'drg';
	protected $_row = [
		'id' => null,
		'code' => '',
		'title' => '',
		'type' => '',
		'weight' => null,
	];

}
