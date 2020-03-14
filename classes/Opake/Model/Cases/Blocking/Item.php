<?php

namespace Opake\Model\Cases\Blocking;

use Opake\Model\AbstractModel;
use Opake\Helper\TimeFormat;

class Item extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'case_blocking_item';
	protected $_row = [
	    'id' => null,
	    'organization_id' => '',
	    'blocking_id' => '',
	    'start' => null,
	    'end' => null,
	    'location_id' => null,
	    'doctor_id' => null,
	    'practice_id' => null,
	    'color' => '',
		'description' => '',
		'overwrite' => 0
	];
	protected $belongs_to = [
		'blocking' => [
			'model' => 'Cases_Blocking',
			'key' => 'blocking_id'
		],
		'location' => [
			'model' => 'location',
			'key' => 'location_id'
		],
		'doctor' => [
			'model' => 'User',
			'key' => 'doctor_id'
		],
		'practice' => [
			'model' => 'PracticeGroup',
			'key' => 'practice_id'
		]
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	public function getValidator() {
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('start')->rule('filled')->rule('date')->error('Incorrect date or time of start');
		$validator->field('end')->rule('filled')->rule('date')->error('Incorrect date or time of end');
		$validator->field('start')->rule('sequence_dates', $this->end)->error('Start time must be earlier end time');
		$validator->field('start')->rule('callback', function($val, $validator, $field){
			$model = $this->pixie->orm->get('Cases_Blocking_Item');
			$model->where([
			    ['start', '<', $this->end],
			    ['end', '>', $this->start],
			    ['location_id', $this->location_id]
			]);
			if ($this->id) {
				$model->where($this->table . '.blocking_id', '!=', $this->blocking_id);
			}
			$model = $model->find();
			return !$model->loaded();
		})->error('Case blocking to the same location at the same time exists');
		return $validator;
	}

	public function fromArray($data) {
		if (isset($data->start) && $data->start) {
			$data->start = strftime(TimeFormat::DATE_FORMAT_DB, strtotime($data->start));
		}
		if (isset($data->end) && $data->end) {
			$data->end = strftime(TimeFormat::DATE_FORMAT_DB, strtotime($data->end));
		}
		if (isset($data->blocking) && $data->blocking) {
			$data->blocking_id = $data->blocking->id;
		}
		if (isset($data->location) && $data->location) {
			$data->location_id = $data->location->id;
		}
		if (isset($data->surgeon_or_practice) && $data->surgeon_or_practice) {
			if (isset($data->surgeon_or_practice->email)) {
				$data->doctor_id = $data->surgeon_or_practice->id;
				$data->practice_id = null;
			} else {
				$data->practice_id = $data->surgeon_or_practice->id;
				$data->doctor_id = null;
			}
		}

		return $data;
	}

	public function getTitle() {
		return 'Block: ' . $this->getSurgeonOrPractice()['fullname'];
	}

	public function toArray()
	{
		$data = parent::toArray();
		$data['overwrite'] = (bool) $this->overwrite;
		$data['surgeon_or_practice'] = $this->getSurgeonOrPractice();

		return $data;
	}

	public function toCalendarArray($colorType)
	{
		if ($this->color) {
			$color = $this->color;
		} else {
			if ($colorType === 'room') {
				$color = $this->location->getCaseColor();
			} else {
				$color = $this->doctor->getCaseColor();
			}
		}

		return [
			'id' => (int) $this->id,
			'className' => [
				'color-' . $color
			],
			'color' => $color,
			'title' => $this->getTitle(),
			'start' => date('D M d Y H:i:s O', strtotime($this->start)),
			'end' => date('D M d Y H:i:s O', strtotime($this->end)),
			'type' => 'block',

			'overwrite' => (bool) $this->overwrite,
			'blocking_id' => (int) $this->blocking_id,
			'location_id' => (int) $this->location_id,
			'location' => $this->location->toArray(),
			'surgeon_or_practice' => $this->getSurgeonOrPractice(),
			'description' => $this->description
		];
	}

	public function getSurgeonOrPractice()
	{
		if ($this->doctor_id) {
			return $this->doctor->toBlockingArray();
		} else if ($this->practice_id) {
			return $this->practice->toExpandedArray($this->organization_id);
		}

		return null;
	}
}
