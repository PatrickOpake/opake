<?php

namespace Opake\Model\Card;

use Opake\Model\AbstractModel;

class Staff extends AbstractModel
{

	const STATUS_OPEN = 1;
	const STATUS_DRAFT = 2;
	const STATUS_SUBMITTED = 3;

	public $id_field = 'id';
	public $table = 'card_staff';
	protected $_row = [
		'id' => null,
		'name' => '',
		'case_id' => null,
		'user_id' => null,
		'additional_note' => '',
		'var_cost' => null,
		'stages' => null,
		'status' => null,
		'template_id' => null,
	];

	protected $belongs_to = [
		'user' => [
			'model' => 'user',
			'key' => 'user_id'
		],
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		],
		'template' => [
			'model' => 'PrefCard_Staff',
			'key' => 'template_id'
		]
	];

	protected $has_many = [
		'items' => [
			'model' => 'Card_Staff_Item',
			'key' => 'card_id',
			'cascade_delete' => true
		],
		'notes' => [
			'model' => 'Card_Staff_Note',
			'key' => 'card_id',
			'cascade_delete' => true
		]
	];

	public function fromArray($data)
	{
		if (!empty($data->stages)) {
			$data->stages = json_encode($data->stages);
		} else {
			$data->stages = null;
		}

		if (isset($data->template) && isset($data->template->id)) {
			$data->template_id = $data->template->id;
		}

		return $data;
	}

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('user_id')->rule('filled')->error('You must specify user');
		$validator->field('case_id')->rule('filled')->error('You must specify case');
		return $validator;
	}

	/**
	 * @return array stages
	 */
	public function getStages($transformToArray = false)
	{
		if ($this->stages) {
			$stages = json_decode($this->stages, $transformToArray);
		}
		if (empty($stages)) {
			$stages = [];
		}
		return $stages;
	}

	public function toArray()
	{
		$data = [
			'id' => (int)$this->id,
			'user_id' => (int)$this->user_id,
			'title' => $this->user->getFullName(),
			'image' => $this->user->getPhoto('tiny'),
			'additional_note' => $this->additional_note,
			'var_cost' => $this->var_cost,
			'stages' => $this->getStages(),
			'status' => $this->status,
		];

		$data['template'] = null;
		if($this->template_id) {
			$data['template'] = $this->template->toArray();
		}


		$items = [];
		foreach ($this->items->with('inventory')->find_all() as $item) {
			$items[] = $item->toArray();
		}
		$data['items'] = $items;

		$notes = [];
		foreach ($this->notes->find_all() as $note) {
			$notes[] = $note->toArray();
		}
		$data['notes'] = $notes;

		return $data;
	}

}
