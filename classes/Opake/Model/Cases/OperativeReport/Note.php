<?php

namespace Opake\Model\Cases\OperativeReport;

use Opake\Model\AbstractModel;

class Note extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'operative_report_note';
	protected $_row = [
		'id' => null,
		'report_id' => null,
		'user_id' => null,
		'time_add' => null,
		'text' => null
	];
	protected $belongs_to = [
		'report' => [
			'model' => 'Cases_OperativeReport',
			'key' => 'report_id'
		],
		'user' => [
			'model' => 'User',
			'key' => 'user_id'
		]
	];

	public function fromArray($data)
	{
		if (isset($data->user) && $data->user) {
			$data->user_id = $data->user->id;
		}
		return $data;
	}

	public function save()
	{
		$this->time_add = strftime('%Y-%m-%d %H:%M:%S');

		parent::save();
	}

	public function toArray()
	{
		return [
			'id' => (int)$this->id,
			'report_id' => (int)$this->report_id,
			'user_id' => (int)$this->user_id,
			'user' => $this->user->toArray(),
			'time_add' => date('D M d Y H:i:s O', strtotime($this->time_add)),
			'text' => $this->text
		];
	}

}
