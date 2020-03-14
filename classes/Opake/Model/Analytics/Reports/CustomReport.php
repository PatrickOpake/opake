<?php

namespace Opake\Model\Analytics\Reports;


use Opake\Model\AbstractModel;

class CustomReport extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'analytics_custom_reports';
	protected $_row = [
		'id' => null,
		'user_id' => null,
		'parent_id' => null,
		'name' => null,
		'columns' => null
	];

	protected $belongs_to = [
		'user' => [
			'model' => 'User',
			'key' => 'user_id',
		],
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\Analytics\Reports\CustomReportsFormatter',
	];
}
