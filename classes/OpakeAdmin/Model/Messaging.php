<?php

namespace OpakeAdmin\Model;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

class Messaging extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'messaging';
	protected $_row = [
		'id' => null,
		'sender_id' => null,
		'recipient_id' => null,
		'send_date' => null,
		'update_date' => null,
		'text' => null,
		'is_read' => 0,
		'active' => 1
	];
	protected $belongs_to = [
		'sender' => [
			'model' => 'User',
			'key' => 'sender_id'
		],
		'recipient' => [
			'model' => 'User',
			'key' => 'recipient_id'
		]
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('text')->rule('filled')->error('You must specify Text');
		return $validator;
	}

	public function save()
	{
		$now = TimeFormat::formatToDBDatetime(new \DateTime());
		if (!$this->send_date) {
			$this->send_date = $now;
		}
		$this->update_date = $now;
		parent::save();
	}

	public function setInactive()
	{
		$this->active = 0;
		$this->save();
	}

	public function toArray()
	{
		return [
			'id' => (int) $this->id,
			'sender_id' => (int) $this->sender_id,
			'text' => $this->text,
			'send_date' => TimeFormat::formatToJsDate($this->send_date),
			'is_read' => (bool) $this->is_read
		];
	}

}
