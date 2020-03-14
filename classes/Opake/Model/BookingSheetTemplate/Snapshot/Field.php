<?php

namespace Opake\Model\BookingSheetTemplate\Snapshot;

use Opake\Model\AbstractModel;

class Field extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'booking_sheet_template_snapshot_fields';
	protected $_row = [
		'id' => null,
		'booking_sheet_template_snapshot_id' => null,
		'field' => null,
		'x' => null,
		'y' => null,
	];

	protected $belongs_to = [
		'snapshot' => [
			'model' => 'BookingSheetTemplate_Snapshot',
			'key' => 'booking_sheet_template_snapshot_id'
		]
	];
}