<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;
use Opake\Model\User;

class InService extends AbstractModel
{
	const TYPE_IN_SERVICE = 1;
	const TYPE_CLEANING = 2;
	const TYPE_MAINTENANCE = 3;
	const TYPE_REPAIR = 4;
	const TYPE_OTHER = 5;

	protected $typeNames = [
		1 => 'In Service ',
		2 => 'Cleaning ',
		3 => 'Maintenance ',
		4 => 'Repair ',
		5 => 'Other'
	];

	public $id_field = 'id';
	public $table = 'case_in_service';
	protected $_row = [
		'id' => null,
		'start' => null,
		'end' => null,
		'organization_id' => '',
		'location_id' => '',
		'type' => null,
		'description' => '',
		'notes_count' => 0,
	];

	protected $belongs_to = [
		'location' => [
			'model' => 'location',
			'key' => 'location_id'
		],
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id'
		]
	];

	protected $has_many = [
		'notes' => [
			'model' => 'Cases_InServiceNote',
			'key' => 'in_service_id',
			'cascade_delete' => true
		]
	];

	public $is_in_service = true;

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('start')->rule('filled')->rule('date')->error('Incorrect date or time of start');
		$validator->field('end')->rule('filled')->rule('date')->error('Incorrect date or time of end');
		$validator->field('start')->rule('sequence_dates', $this->end)->error('Service length must be positive');
		$validator->field('location_id')->rule('filled')->error('You must specify room');
		$validator->field('description')->rule('max_length', 10000)->error('The Description must be less than or equal to 10000 characters');
		$validator->field('start')->rule('callback', function($val, $validator, $field){
			$model = $this->pixie->orm->get('Cases_InService');
			$model->where([
				['start', '<', $this->end],
				['end', '>', $this->start],
				['location_id', $this->location_id]
			]);
			if ($this->id) {
				$model->where($this->table . '.id', '!=', $this->id);
			}
			$model = $model->find();
			return !$model->loaded();
		})->error('InService to the same location at the same time exists');

		return $validator;
	}

	public function getNotes()
	{
		return $this->notes->find_all()->as_array();
	}

	public function fromArray($data)
	{
		if (isset($data->start) && $data->start) {
			$data->start = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->start));
		}
		if (isset($data->end) && $data->end) {
			$data->end = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->end));
		}
		if (isset($data->location) && $data->location) {
			$data->location_id = $data->location->id;
		}
		if (isset($data->service_type) && $data->service_type) {
			$data->type = $data->service_type;
		}

		unset($data->notes_count);

		return $data;
	}

	public function toArray()
	{
		$data = [
			'id' => (int)$this->id,
			'title' => $this->getTitle(),
			'organization_id' => (int)$this->organization_id,
			'location' => $this->location->toArray(),
			'service_type' => $this->type,
			'description' => $this->description,
			'start' => date('D M d Y H:i:s O', strtotime($this->start)),
			'end' => date('D M d Y H:i:s O', strtotime($this->end)),
			'notes_count' => (int)$this->notes_count
		];

		return $data;
	}

	public function toCalendarArray()
	{
		$data = [
			'id' => (int)$this->id,
			'title' => $this->getTitle(),
			'type' => 'in_service',
			'start' => date('D M d Y H:i:s O', strtotime($this->start)),
			'end' => date('D M d Y H:i:s O', strtotime($this->end)),
			'allDay' => false,
			'service_type' => $this->type,
			'description' => $this->description,
			'location_id' => (int)$this->location_id,
			'location' => $this->location->toArray(),
		];

		return $data;
	}

	public function toDashboardArray()
	{
		$data = [
			'id' => (int) $this->id,
			'title' => $this->getTitle(),
			'time_start' => date('D M d Y H:i:s O', strtotime($this->start)),
			'time_end' => date('D M d Y H:i:s O', strtotime($this->end)),
			'description' => $this->description,
			'location_id' => (int) $this->location_id,
			'is_in_service' => true,
			'notes_count' => (int)$this->notes_count
		];

		return $data;
	}

	public function getTitle()
	{
		if ($this->type) {
			return $this->typeNames[$this->type];
		} else {
			return 'In Service';
		}
	}

	public function updateNotesCount()
	{
		$this->conn->query('update')->table($this->table)
			->data(['notes_count' => $this->pixie->db->expr('notes_count + 1')])
			->where('id', $this->id)
			->execute();
	}

	public function reduceNotesCount()
	{
		$this->conn->query('update')->table($this->table)
			->data(['notes_count' => $this->pixie->db->expr('notes_count - 1')])
			->where('id', $this->id)
			->execute();
	}

	public function hasUnreadNotesForUser($userId)
	{
		$query = $this->pixie->db->query('select')
			->table('user_in_service_note')
			->fields('last_read_note_id')
			->where([['user_id', $userId], ['in_service_id', $this->id]])
			->execute()
			->current();

		if (!$query) {
			$this->pixie->db->query('insert')
				->table('user_in_service_note')
				->data(['user_id' => $userId, 'in_service_id' => $this->id])
				->execute();

			return true;
		} else {
			$lastReadNoteId = $query->last_read_note_id;
			$inServiceNotes = $this->notes->find_all()->as_array();

			if (count($inServiceNotes) && ($lastReadNoteId < $inServiceNotes[count($inServiceNotes) - 1]->id)) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function readNotes($userId)
	{
		$note = $this->notes->order_by('id', 'DESC')->limit(1)->find();
		$lastNoteId = $note->id;

		$this->pixie->db->query('update')
			->table('user_in_service_note')
			->data(['last_read_note_id' => $lastNoteId])
			->where([['user_id', $userId], ['in_service_id', $this->id]])
			->execute();
	}
}
