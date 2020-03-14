<?php

namespace Opake\Model\PrefCard\Staff;

use Opake\Model\AbstractModel;

class Note extends AbstractModel
{
	public $table = 'pref_card_staff_note';
	public $id_field = 'id';
	protected $_row = [
		'id' => null,
		'card_id' => null,
		'name' => '',
		'text' => '',
	];

	public function toArray()
	{
		return [
			'id' => $this->id(),
			'name' => $this->name,
			'text' => $this->text
		];
	}

}
