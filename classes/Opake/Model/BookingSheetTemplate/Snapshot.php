<?php

namespace Opake\Model\BookingSheetTemplate;

use Opake\Model\AbstractModel;

class Snapshot extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'booking_sheet_template_snapshot';
	protected $_row = [
		'id' => null,
		'booking_id' => null,
		'original_template_id' => null,
	];

	protected $belongs_to = [
		'original_template' => [
			'model' => 'BookingSheetTemplate',
			'key' => 'original_template_id'
		],
	    'booking' => [
		    'model' => 'Booking',
	        'key' => 'booking_id'
	    ]
	];

	protected $has_many = [
		'fields' => [
			'model' => 'BookingSheetTemplate_Snapshot_Field',
			'key' => 'booking_sheet_template_snapshot_id',
			'cascade_delete' => true
		]
	];

	protected $formatters = [
		'TemplateBookingSheet' => [
			'class' => '\Opake\Formatter\BookingSheetTemplate\Snapshot\TemplateFormatter'
		]
	];

}