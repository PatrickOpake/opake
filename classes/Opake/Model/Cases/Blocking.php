<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;
use Opake\Helper\TimeFormat;

class Blocking extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'case_blocking';
	protected $_row = [
		'id' => null,
		'organization_id' => '',
		'location_id' => '',
		'doctor_id' => '',
		'practice_id' => '',
		'color' => '',
		'duration' => '',
		'date_from' => null,
		'date_to' => null,
		'time_from' => null,
		'time_to' => null,
		'recurrence_every' => null,
		'daily_every' => '',
		'monthly_every' => '',
		'day_number' => null,
		'week_number' => null,
		'recurrence_week_days' => '',
		'month_number' => null,
		'recurrence_monthly_day' => '',
		'monthly_day' => '',
		'monthly_week' => '',
		'description' => '',
		'overwrite' => 0
	];
	protected $belongs_to = [
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
	protected $has_many = [
		'items' => [
			'model' => 'Cases_Blocking_Item',
			'key' => 'blocking_id',
			'cascade_delete' => true
		]
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	const RECURRENCE_DAILY = 1;
	const RECURRENCE_WEEKLY = 2;
	const RECURRENCE_MONTHLY = 3;
	const MONDAY = 1;
	const TUESDAY = 2;
	const WEDNESDAY = 3;
	const THURSDAY = 4;
	const FRIDAY = 5;
	const SATURDAY = 6;
	const SUNDAY = 7;

	protected static $recurrence_period = [
		self::RECURRENCE_DAILY => 'DAILY',
		self::RECURRENCE_WEEKLY => 'WEEKLY',
		self::RECURRENCE_MONTHLY => 'MONTHLY'
	];
	protected static $week_days = [
		self::MONDAY => 'MO',
		self::TUESDAY => 'TU',
		self::WEDNESDAY => 'WE',
		self::THURSDAY => 'TH',
		self::FRIDAY => 'FR',
		self::SATURDAY => 'SA',
		self::SUNDAY => 'SU'
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('date_from')->rule('filled')->rule('date')->error('Incorrect date of start');
		$validator->field('date_to')->rule('filled')->rule('date')->error('Incorrect date of end');
		$validator->field('location_id')->rule('filled')->error('You must specify room');
		$validator->field('recurrence_every')->rule('filled')->error('You must specify frequency');

		if (!$this->doctor_id) {
			$validator->field('practice_id')->rule('filled')->error('You must specify surgeon or practice group');
		} elseif (!$this->practice_id) {
			$validator->field('doctor_id')->rule('filled')->error('You must specify surgeon or practice group');
		}

		if ($this->recurrence_every == self::RECURRENCE_DAILY) {
			$validator->field('daily_every')->rule('filled')->error('You must specify daily recurrence options');
			if ($this->daily_every == 'day') {
				$validator->field('day_number')->rule('filled')->error('You must specify day number');
			}
		} elseif ($this->recurrence_every == self::RECURRENCE_WEEKLY) {
			$validator->field('week_number')->rule('filled')->error('You must specify week number');
			$validator->field('recurrence_week_days')->rule('filled')->error('You must specify days of week');
		} elseif ($this->recurrence_every == self::RECURRENCE_MONTHLY) {
			$validator->field('monthly_every')->rule('filled')->error('You must specify monthly recurrence options');
			$validator->field('month_number')->rule('filled')->error('You must specify month number');
			if ($this->monthly_every == 'day') {
				$validator->field('recurrence_monthly_day')->rule('filled')->error('You must specify monthly day number');
			} elseif ($this->monthly_every == 'weekday') {
				$validator->field('monthly_week')->rule('filled')->error('You must specify monthly week number');
				$validator->field('monthly_day')->rule('filled')->error('You must specify monthly day of week');
			}
		}

		return $validator;
	}

	public function fromArray($data)
	{
		if (isset($data->surgeon_or_practice) && $data->surgeon_or_practice) {
			if (isset($data->surgeon_or_practice->email)) {
				$data->doctor_id = $data->surgeon_or_practice->id;
				$data->practice_id = null;
			} else {
				$data->practice_id = $data->surgeon_or_practice->id;
				$data->doctor_id = null;
			}
		}

		if (isset($data->date_from) && $data->date_from) {
			$data->date_from = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->date_from));
		}
		if (isset($data->date_to) && $data->date_to) {
			$data->date_to = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->date_to));
		}
		if (isset($data->time_from) && $data->time_from) {
			$data->time_from = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->time_from));
		}
		if (isset($data->time_to) && $data->time_to) {
			$data->time_to = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->time_to));
		}
		if (isset($data->location) && $data->location) {
			$data->location_id = $data->location->id;
		}
		if (isset($data->recurrence_week_days) && $data->recurrence_week_days) {
			$data->recurrence_week_days = serialize($data->recurrence_week_days);
		} else {
			$data->recurrence_week_days = '';
		}

		return $data;
	}

	public function toArray()
	{
		$data = parent::toArray();
		$data['recurrence_week_days'] = unserialize($this->recurrence_week_days);
		$data['overwrite'] = (bool) $this->overwrite;
		$data['surgeon_or_practice'] = $this->getSurgeonOrPractice();

		return $data;
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

	public function getFrequency()
	{
		return self::$recurrence_period[$this->recurrence_every];
	}

	public function getEndTime($startTime)
	{
		$timeTo = TimeFormat::fromDBTime($this->time_to);
		$startTime->setTime($timeTo->format('H'), $timeTo->format('i'), $timeTo->format('s'));
		return $startTime;
	}

	public function getByWeekDay()
	{
		$result = '';
		$week_days = unserialize($this->recurrence_week_days);
		foreach ($week_days as $key => $day) {
			$result .= self::$week_days[$day];
			if ($key !== count($week_days) - 1) {
				$result .= ',';
			}
		}

		return $result;
	}

	public function getByMonthDay()
	{
		return $this->getMonthNumber() . self::$week_days[$this->monthly_day];
	}

	public function getWeekNumber()
	{
		if ($this->week_number == 5) {
			return -1;
		} else {
			return $this->week_number;
		}
	}

	public function getMonthNumber()
	{
		if ($this->monthly_week == 5) {
			return -1;
		} else {
			return $this->monthly_week;
		}
	}

	public function getRuleSet()
	{
		$ruleset = 'FREQ=' . $this->getFrequency() . ';';

		if ($this->recurrence_every == self::RECURRENCE_DAILY) {
			if ($this->daily_every == 'weekday') {
				$ruleset .= 'BYDAY=MO,TU,WE,TH,FR;';
			} else if ($this->daily_every == 'day') {
				$ruleset .= 'INTERVAL=' . $this->day_number . ';';
			}
		}

		if ($this->recurrence_every == self::RECURRENCE_WEEKLY) {
			$ruleset .= 'BYDAY=' . $this->getByWeekDay() . ';INTERVAL=' . $this->week_number . ';';
		}

		if ($this->recurrence_every == self::RECURRENCE_MONTHLY) {
			if ($this->monthly_every == 'weekday') {
				$ruleset .= 'BYDAY=' . $this->getByMonthDay() . ';INTERVAL=' . $this->month_number . ';';
			} else if ($this->monthly_every == 'day') {
				$ruleset .= 'BYYEARDAY=' . $this->recurrence_monthly_day . ';INTERVAL=' . $this->month_number . ';';
			}
		}

		return $ruleset;
	}

	public function transformBlocks()
	{
		$dateFrom = new \DateTime($this->date_from);
		$dateTo = new \DateTime($this->date_to);

		$startDate = new \DateTime($this->time_from);
		$startDate->setDate($dateFrom->format('Y'), $dateFrom->format('m'), $dateFrom->format('d'));
		$endDate = null;
		if ($this->time_to) {
			$endDate = new \DateTime($this->time_to);
			$endDate->setDate($dateTo->format('Y'), $dateTo->format('m'), $dateTo->format('d'));
		}
		$ruleset = $this->getRuleSet();

		$rule = new \Recurr\Rule($ruleset, $startDate, $endDate);
		$transformer = new \Recurr\Transformer\ArrayTransformer();
		$constraint = new \Recurr\Transformer\Constraint\BeforeConstraint($endDate);
		return $transformer->transform($rule, null, $constraint);
	}

}
