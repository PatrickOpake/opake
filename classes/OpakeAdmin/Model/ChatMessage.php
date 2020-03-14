<?php

namespace OpakeAdmin\Model;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

class ChatMessage extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'chat_message';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'user_id' => null,
		'date' => null,
		'text' => null
	];
	protected $belongs_to = [
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id'
		],
		'user' => [
			'model' => 'User',
			'key' => 'user_id'
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
		if (!$this->date) {
			$this->date = TimeFormat::formatToDBDatetime(new \DateTime());
		}
		parent::save();
	}

	public function toArray()
	{
		return [
			'id' => (int)$this->id,
			'user' => $this->user->toShortArray(),
			'user_full_name' => $this->user->getFullName(),
			'date' => $this->date,
			'text' => $this->text
		];
	}

}
