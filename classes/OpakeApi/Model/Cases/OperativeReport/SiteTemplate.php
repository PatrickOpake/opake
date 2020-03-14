<?php

namespace OpakeApi\Model\Cases\OperativeReport;

use Opake\Model\Cases\OperativeReport\SiteTemplate as OpakeOperativeReportTemplate;
use Opake\Model\Profession;

class SiteTemplate extends OpakeOperativeReportTemplate {

	const FIELD_GROUP_CASE_INFO = 1;
	const FIELD_GROUP_DESC = 2;
	const FIELD_GROUP_MATERIALS = 3;
	const FIELD_GROUP_CONCLUSIONS = 4;
	const FIELD_GROUP_FOLLOW_UP = 5;

	protected static $fieldGroups = [
		self::FIELD_GROUP_CASE_INFO => 'Case Information',
		self::FIELD_GROUP_DESC => 'Descriptions',
		self::FIELD_GROUP_MATERIALS => 'Materials',
		self::FIELD_GROUP_CONCLUSIONS => 'Conclusions',
		self::FIELD_GROUP_FOLLOW_UP => 'Follow Up',
	];

	public static function getFieldGroups()
	{
		return self::$fieldGroups;
	}

	public static $staffProfession = [
		'surgeon' => Profession::SURGEON,
		'other_staff' => null,
		'co_surgeon' => Profession::SURGEON,
		'supervising_surgeon' => Profession::SURGEON,
		'first_assistant_surgeon' => Profession::SURGEON,
		'assistant' => Profession::SURGEON,
		'anesthesiologist' => Profession::ANESTHESIOLOGIST,
		'dictated_by' => Profession::SURGEON,
	];

	public static $additionalAPIFields = [
		'dob' => [
			'group_id' => self::GROUP_CASE_INFO_ID,
			'name' => 'Date of Birth',
			'type' => self::FIELD_TYPE_DATE,
			'sort' => 1,
			'show' => 'date',
		],
		'mrn' => [
			'group_id' => self::GROUP_CASE_INFO_ID,
			'name' => 'MRN',
			'type' => self::FIELD_TYPE_TEXT,
			'sort' => 2,
			'show' => 'input',
		],
		'admit_type' => [
			'group_id' => self::GROUP_CASE_INFO_ID,
			'name' => 'Admit Type',
			'type' => self::FIELD_TYPE_ADMIT_TYPE,
			'sort' => 3,
			'show' => 'input',
		],
		'staff' => [
			'group_id' => self::GROUP_CASE_INFO_ID,
			'name' => 'Staff',
			'type' => self::FIELD_TYPE_ARRAY,
			'sort' => 1,
			'show' => 0,
		],
	];

	public static function fieldsProps()
	{
		$data = parent::fieldsProps();
		return array_merge($data, self::$additionalAPIFields);
	}

	public static function getGroupIdByField($field)
	{
		return self::getPropField($field, 'group_id');

	}

	public static function getNameByField($field)
	{
		return self::getPropField($field, 'name');

	}

	public static function getTypeByField($field)
	{
		return self::getPropField($field, 'type');

	}

	public static function getSortByField($field)
	{
		//fixme: force string for API
		return (string) self::getPropField($field, 'sort');
	}

	public static function getShowByField($field)
	{
		return self::getPropField($field, 'show');
	}

	public function toArray()
	{
		//fixme: force string for API
		$data = parent::toArray();

		if (isset($data['sort'])) {
			$data['sort'] = (string) $data['sort'];
		}

		return $data;
	}

	protected static function getPropField($field, $propName)
	{
		$groups = self::fieldsProps();
		if(isset($groups[$field][$propName])) {
			return $groups[$field][$propName];
		} else {
			return null;
		}
	}

}
