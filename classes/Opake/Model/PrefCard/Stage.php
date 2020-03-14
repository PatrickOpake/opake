<?php

namespace Opake\Model\PrefCard;

use Opake\Model\AbstractModel;

class Stage extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'pref_card_stage';
	protected $_row = [
		'id' => null,
		'name' => '',
		'is_requested_items' => 0
	];

}
