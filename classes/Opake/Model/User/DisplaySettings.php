<?php

namespace Opake\Model\User;

use Opake\Model\AbstractModel;

class DisplaySettings extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'surgeon_display_settings';
	protected $_row = [
		'id' => null,
		'user_id' => null,
		'overview_position' => null
	];

	protected $belongs_to = [
		'user' => [
			'model' => 'User',
			'key' => 'user_id',
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