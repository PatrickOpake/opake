<?php

namespace Opake\Model\Cases\OperativeReport;

use Opake\Model\AbstractModel;

class Amendment extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'case_op_report_amendment';
	protected $_row = [
		'id' => null,
		'report_id' => null,
		'time_signed' => null,
		'user_signed' => null,
		'text' => '',
	];

	protected $belongs_to = [
		'signed_user' => [
			'model' => 'User',
			'key' => 'user_signed'
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
