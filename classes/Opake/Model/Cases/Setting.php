<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;

class Setting extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'case_setting';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'block_timing' => self::BLOCK_TIMING_48_HOURS,
		'block_overwrite' => 0,
		'display_timestamp_on_printout' => 0
	];

	const BLOCK_TIMING_NEVER = 1;
	const BLOCK_TIMING_12_HOURS = 2;
	const BLOCK_TIMING_24_HOURS = 3;
	const BLOCK_TIMING_48_HOURS = 4;
	const BLOCK_TIMING_96_HOURS = 5;

	protected static $block_timings = [
		self::BLOCK_TIMING_NEVER => 'Never',
		self::BLOCK_TIMING_12_HOURS => '12 Hours',
		self::BLOCK_TIMING_24_HOURS => '24 Hours',
		self::BLOCK_TIMING_48_HOURS => '48 Hours',
		self::BLOCK_TIMING_96_HOURS => '96 Hours'
	];

	protected static $timing_hours = [
		self::BLOCK_TIMING_NEVER => 0,
		self::BLOCK_TIMING_12_HOURS => 12,
		self::BLOCK_TIMING_24_HOURS => 24,
		self::BLOCK_TIMING_48_HOURS => 48,
		self::BLOCK_TIMING_96_HOURS => 96
	];

	protected static $colors = [
		'#BB96CE' => 'Purple',
		'#8FAFE0' => 'Sky Blue',
		'#ED7B7B' => 'Apricot',
		'#58F4B3' => 'Aquamarine',
		'#E8C188' => 'Gold Sand',
		'#CECECE' => 'Grey',
		'#E0E0E0' => 'Default Grey'
	];

	public function getBlockTiming()
	{
		return self::$block_timings[$this->block_timing];
	}

	public function getTimingHour()
	{
		return self::$timing_hours[$this->block_timing];
	}

	public static function getDefaultBlockHour()
	{
		return self::$timing_hours[self::BLOCK_TIMING_48_HOURS];
	}

	public function toArray()
	{
		$data = parent::toArray();
		$data['block_timing'] = (int)$this->block_timing;
		$data['block_overwrite'] = (int)$this->block_overwrite;
		return $data;
	}

	public static function getColors()
	{
		return static::$colors;
	}

	public static function getBlockTimings()
	{
		return static::$block_timings;
	}

}
