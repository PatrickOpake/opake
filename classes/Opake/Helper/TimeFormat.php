<?php

namespace Opake\Helper;

class TimeFormat
{

	const DATE_FORMAT_DB = '%Y-%m-%d %H:%M:%S';
	const TIME_FORMAT_DB = '%H:%M:%S';


	public static function getDateTime($date)
	{
		return static::_getTimeWithFormat($date, 'n/j/Y g:i A');
	}

	public static function getDate($date)
	{
		return static::_getTimeWithFormat($date, 'n/j/Y');
	}

	public static function getDateWithLeadingZeros($date)
	{
		return static::_getTimeWithFormat($date, 'm/d/Y');
	}

	public static function getTime($date)
	{
		return static::_getTimeWithFormat($date, 'g:i A');
	}

	public static function getTimeWithSeconds($date)
	{
		return static::_getTimeWithFormat($date, 'g:i:s A');
	}

	protected static function _getTimeWithFormat($date, $format)
	{
		if (!$date || (is_string($date) && substr($date, 0, 10) == '0000-00-00')) {
			return '';
		}
		if (!($date instanceof \DateTime)) {
			$date = new \DateTime($date);
		}
		return $date->format($format);
	}


	public static function fromDatepickerToDBDate($date)
	{
		if (!$date) {
			return null;
		}

		$date = \DateTime::createFromFormat('m/d/Y', $date);
		if (!$date) {
			return null;
		}

		return $date->format('Y-m-d');
	}

	/**
	 * @param \DateTime $date
	 * @return string
	 */
	public static function formatToDB($date)
	{
		if (!$date) {
			return '';
		}
		if (!($date instanceof \DateTime)) {
			$date = new \DateTime($date);
		}
		return $date->format('Y-m-d');
	}

	/**
	 * @param \DateTime $date
	 * @return string
	 */
	public static function formatToDBDatetime($date)
	{
		if (!$date) {
			return '';
		}
		if (!($date instanceof \DateTime)) {
			$date = new \DateTime($date);
		}
		return $date->format('Y-m-d H:i:s');
	}

	/**
	 * @param \DateTime $date
	 * @return null|string
	 */
	public static function formatToJsDate($date)
	{
		if (!$date) {
			return null;
		}
		if (!($date instanceof \DateTime)) {
			$date = new \DateTime($date);
		}
		return $date->format('D M d Y H:i:s O');
	}

	/**
	 * @param $date
	 * @return \DateTime
	 */
	public static function fromDBTime($date)
	{
		return \DateTime::createFromFormat('H:i:s', $date);
	}

	/**
	 * @param $date
	 * @return \DateTime
	 */
	public static function fromDBDate($date)
	{
		return \DateTime::createFromFormat('Y-m-d', $date);
	}

	/**
	 * @param $date
	 * @return \DateTime
	 */
	public static function fromDBDatetime($date)
	{
		return \DateTime::createFromFormat('Y-m-d H:i:s', $date);
	}

}
