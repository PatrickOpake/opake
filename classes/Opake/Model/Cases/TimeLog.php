<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;

class TimeLog extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'case_time_log';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'stage' => '',
		'time' => ''
	];

	protected $has_many = [
		'staff' => [
			'model' => 'User',
			'through' => 'case_time_log_staff',
			'key' => 'timelog_id',
			'foreign_key' => 'staff_id'
		]

	];

	public function fromArray($data)
	{
		if (isset($data->staff) && $data->staff) {
			$staff = [];
			foreach ($data->staff as $user) {
				$staff[] = $user->id;
			}
			$data->staff = $staff;
		}
		if(isset($data->time) && $data->time) {
			$data->time = strftime(\Opake\Helper\TimeFormat::TIME_FORMAT_DB, strtotime($data->time));
		} else {
			$data->time = null;
		}

		return $data;
	}

	public function toArray()
	{
		$data = parent::toArray();

		$staff = [];
		foreach ($this->staff->find_all() as $user) {
			$staff[] = $user->toArray();
		}
		$data['staff'] = $staff;

		return $data;
	}

}
