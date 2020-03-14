<?php

namespace Opake\Model\PrefCard;

use Opake\Model\AbstractModel;
use Opake\Helper\TimeFormat;

class Staff extends AbstractModel
{
	public $table = 'pref_card_staff';
	public $id_field = 'id';
	protected $_row = [
		'id' => null,
		'name' => '',
		'user_id' => null,
		'create_date' => null,
		'last_edit_date' => null,
		'stages' => null
	];
	protected $belongs_to = [
		'user' => [
			'model' => 'user',
			'key' => 'user_id'
		]
	];
	protected $has_many = [
		'items' => [
			'model' => 'PrefCard_Staff_Item',
			'key' => 'card_id',
			'cascade_delete' => true
		],
		'notes' => [
			'model' => 'PrefCard_Staff_Note',
			'key' => 'card_id',
			'cascade_delete' => true
		],
		'case_types' => [
			'model' => 'Cases_Type',
			'through' => 'pref_card_staff_case_type',
			'key' => 'pref_card_staff_id',
			'foreign_key' => 'type_id',
			'overwrite' => true
		]
	];

	protected $formatters = [
		'DashboardPrint' => [
			'class' => '\Opake\Formatter\PrefCard\Staff\DashboardPrintFormatter'
		]
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('user_id')->rule('filled')->error('You must specify user');
		$validator->field('name')->rule('filled')->error('You must specify Template Name');
		return $validator;
	}

	/**
	 * @param bool $transformToArray
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

	public function save()
	{
		$this->last_edit_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
		if (!$this->id) {
			$this->create_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
		}
		parent::save();
	}

	public function fromArray($data)
	{
		if (isset($data->user) && $data->user) {
			$data->user_id = $data->user->id;
		}

		if (isset($data->case_types) && $data->case_types) {
			$case_types = [];
			foreach ($data->case_types as $type) {
				$case_types[] = $type->id;
			}
			$data->case_types = $case_types;
		}

		if (!empty($data->stages)) {
			$data->stages = json_encode($data->stages);
		} else {
			$data->stages = null;
		}

		return $data;
	}

	public function getByOrganization($currentOrgId)
	{
		$query = $this->query;
		$query->fields($this->table . '.*');
		$query->join('user', ['user.id', $this->table . '.user_id'])
		->where('user.organization_id', $currentOrgId);
		return $this;
	}

	public function toArray()
	{
		$data = [
			'id' => (int) $this->id,
			'name' => $this->name,
			'stages' => $this->getStages(),
			'user' => [
				'id' => (int) $this->user->id,
				'image' => $this->user->getPhoto('tiny'),
				'full_name' => $this->user->getFullName(),
			],
		];

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

		$caseTypes = [];
		foreach ($this->case_types->find_all() as $type) {
			$caseTypes[] = $type->toArray();
		}
		$data['case_types'] = $caseTypes;

		return $data;
	}

	public function toShortArray()
	{
		return [
			'id' => (int)$this->id,
			'name' => $this->name,
			'user_id' => (int)$this->user_id,
			'title' => $this->user->getFullName(),
			'image' => $this->user->getPhoto('tiny'),
			'last_edit_date' => TimeFormat::getDate($this->last_edit_date)
		];
	}

}
