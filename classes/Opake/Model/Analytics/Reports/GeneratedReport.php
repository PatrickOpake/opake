<?php

namespace Opake\Model\Analytics\Reports;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

class GeneratedReport extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'analytics_generated_reports';
	protected $_row = [
		'id' => null,
		'file_id' => null,
		'key' => null
	];

	protected $belongs_to = [
		'file' => [
			'model' => 'UploadedFile',
			'key' => 'file_id',
		],
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	/**
	 * @return string
	 */
	public function generateAccessKey()
	{
		$this->key = md5(uniqid());

		return $this->key;
	}

}