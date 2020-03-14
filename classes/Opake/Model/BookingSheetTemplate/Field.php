<?php

namespace Opake\Model\BookingSheetTemplate;

use Opake\Model\AbstractModel;

class Field extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'booking_sheet_template_fields';
	protected $_row = [
		'id' => null,
		'booking_sheet_template_id' => null,
		'field' => null,
		'x' => null,
		'y' => null,
	    'active' => 0
	];

	protected $belongs_to = [
		'template' => [
			'model' => 'BookingSheetTemplate',
			'key' => 'booking_sheet_template_id'
		]
	];
}