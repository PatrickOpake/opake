<?php

namespace Opake\Model\Billing;

use Opake\Model\AbstractModel;
use Opake\Model\NoteTrait;
use Opake\Model\ReminderNote;

class Note extends AbstractModel
{
	use NoteTrait;

	public $id_field = 'id';
	public $table = 'billing_note';
	protected $type_note = ReminderNote::TYPE_NOTE_BILLING;
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'user_id' => null,
		'patient_id' => null,
		'time_add' => null,
		'text' => null
	];
	protected $belongs_to = [
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		],
		'user' => [
			'model' => 'User',
			'key' => 'user_id'
		]
	];
	protected $has_one = [
		'reminder' => [
			'model' => 'ReminderNote',
			'key' => 'note_id'
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
		$reminder = $this->getReminder();
		return [
			'id' => (int)$this->id,
			'case_id' => (int)$this->case_id,
			'user_id' => (int)$this->user_id,
			'flagged' => (isset($this->patient_id) && !empty($this->patient_id)) ? true : false,
			'user' => $this->user->toArray(),
			'time_add' => date('D M d Y H:i:s O', strtotime($this->time_add)),
			'text' => $this->text,
			'reminder' => $reminder ? $reminder->toArray() : null,
		];
	}

}
