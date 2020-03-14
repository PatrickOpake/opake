<?php

namespace Opake\Model\Card\Staff;

use Opake\Model\AbstractModel;

class Note extends AbstractModel
{

	public $table = 'card_staff_note';
	protected $_row = [
		'id' => null,
		'card_id' => null,
		'name' => '',
		'text' => '',
		'is_checked' => null
	];

	public function toArray()
	{
		return [
			'id' => $this->id(),
			'name' => $this->name,
			'text' => $this->text,
			'is_checked' => (bool) $this->is_checked
		];
	}

	public function getCard()
	{
		return $this->pixie->orm->get('Card_Staff', $this->card_id);
	}

}
