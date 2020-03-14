<?php

namespace Opake\Model\Inventory;

use Opake\Model\AbstractModel;

class Code extends AbstractModel
{

	public $table = 'inventory_code';
	protected $_row = [
		'inventory_id' => null,
		'type' => null,
		'code' => '',
	];

	public function toArray()
	{
		return [
			'type' => $this->type,
			'code' => $this->code
		];
	}

	const TYPE_OTHER = 0;
	const TYPE_BARCODE = 1;

	public function save()
	{
		$query = $this->conn->query('insert')->table($this->table);
		$query->data($this->_row);
		$query->execute();
	}

}
