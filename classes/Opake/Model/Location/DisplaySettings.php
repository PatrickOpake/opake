<?php

namespace Opake\Model\Location;

use Opake\Model\AbstractModel;

class DisplaySettings extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'room_display_settings';
	protected $_row = [
		'id' => null,
		'location_id' => null,
		'overview_position' => null
	];

	protected $belongs_to = [
		'location' => [
			'model' => 'Location',
			'key' => 'location_id',
		]
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];
}