<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;

class InServiceNote extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'in_service_note';
	protected $_row = [
		'id' => null,
		'in_service_id' => null,
		'user_id' => null,
		'time_add' => null,
		'text' => null
	];

	protected $belongs_to = [
		'in_service' => [
			'model' => 'Cases_InService',
			'key' => 'in_service_id'
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
			'in_service_id' => (int)$this->in_service_id,
			'user_id' => (int)$this->user_id,
			'user' => $this->user->toArray(),
			'time_add' => date('D M d Y H:i:s O', strtotime($this->time_add)),
			'text' => $this->text
		];
	}

}
