<?php

namespace Opake\Model\Cases\OperativeReport;

use Opake\Model\AbstractModel;

class SiteTemplate extends AbstractModel {

	public $id_field = 'id';
	public $table = 'case_op_report_site_template';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'field' => '',
		'name' => '',
		'group_id' => null,
		'sort' => null,
		'active' => 0,
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	const GROUP_CASE_INFO_ID = 1;
	const GROUP_DESCRIPTIONS_ID = 2;
	const GROUP_MATERIALS_ID = 3;
	const GROUP_CONCLUSIONS_ID = 4;
	const GROUP_FOLLOW_UP_ID = 5;

	const FIELD_TYPE_DATE = 'date';
	const FIELD_TYPE_TEXT = 'text';
	const FIELD_TYPE_ARRAY = 'array';
	const FIELD_TYPE_LOCATION = 'location';
	const FIELD_TYPE_USER = 'user';
	const FIELD_TYPE_CASE_TYPE = 'case_type';
	const FIELD_TYPE_DIAGNOSIS = 'diagnosis';
	const FIELD_TYPE_ADMIT_TYPE = 'admit_type';

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('name')->rule('unique', $this)->error(sprintf("Field with name %s already exist", $this->name));
		return $validator;
	}

	public function fromArray($data)
	{
		if($data->field !== 'custom') {
			$data->name = null;
		}
		return $data;
	}

	public static function getFields() {
		return [
			'staff' => true,
			'dob' => true,
			'mrn' => true,
			'admit_type' => false,
			'room' => true,
			'patient_name' => true,
			'age_sex' => true,
			'dos' => true,
			'acc_number' => true,
			'time_scheduled' => true,
			'procedure_case' => true,

			'surgeon' => true,
			'anesthesiologist' => true,
			'other_staff' => false,
			'co_surgeon' => false,
			'supervising_surgeon' => false,
			'first_assistant_surgeon' => false,
			'assistant' => false,
			'dictated_by' => false,

			'procedure' => true,
			'pre_op_diagnosis' => true,
			'post_op_diagnosis' => true,
			'consent' => false,
			'clinical_history' => false,
			'anesthesia_administered' => false,
			'approach' => false,
			'description_procedure' => false,
			'complications' => false,
			'specimens_removed' => false,
			'total_tourniquet_time' => false,
			'ebl' => false,
			'blood_transfused' => false,
			'fluids' => false,
			'drains' => false,
			'urine_output' => false,
			'findings' => false,
			'operation_time' => true,
			'follow_up_care' => false,
			'conditions_for_discharge' => false,
			'scribe' => false,
		];
	}

	public static function fieldsProps()
	{
		return [
			'patient_name' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Patient Name',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 0,
				'show' => 'input',
			],
			'age_sex' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Age/Sex',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 1,
				'show' => 'input',
			],
			'dob' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Date of Birth',
				'type' => self::FIELD_TYPE_DATE,
				'sort' => 2,
				'show' => 'input',
			],
			'mrn' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'MRN',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 3,
				'show' => 'input',
			],
			'admit_type' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Admit Type',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 3,
				'show' => 'only_future',
			],
			'room' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Room',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 4,
				'show' => 'input',
			],
			'dos' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Date of Service',
				'type' => self::FIELD_TYPE_DATE,
				'sort' => 5,
				'show' => 'input',
			],
			'acc_number' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Account #',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 6,
				'show' => 'input',
			],
			'time_scheduled' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Time',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 7,
				'show' => 'input',
			],
			'procedure' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Procedure',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 8,
				'show' => 'only_future',
			],
			'surgeon' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Surgeon',
				'type' => self::FIELD_TYPE_USER,
				'sort' => 9,
				'show' => 'users',
			],
			'other_staff' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Other Staff',
				'type' => self::FIELD_TYPE_USER,
				'sort' => 10,
				'show' => 'users',
			],
			'co_surgeon' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Co-Surgeon',
				'type' => self::FIELD_TYPE_USER,
				'sort' => 11,
				'show' => 'users',
			],
			'supervising_surgeon' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Supervising Surgeon',
				'type' => self::FIELD_TYPE_USER,
				'sort' => 12,
				'show' => 'users',
			],
			'first_assistant_surgeon' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'First Assistant Surgeon',
				'type' => self::FIELD_TYPE_USER,
				'sort' => 13,
				'show' => 'users',
			],
			'assistant' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Assistant',
				'type' => self::FIELD_TYPE_USER,
				'sort' => 14,
				'show' => 'users',
			],
			'anesthesiologist' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Anesthesiologist',
				'type' => self::FIELD_TYPE_USER,
				'sort' => 15,
				'show' => 'users',
			],
			'dictated_by' => [
				'group_id' => self::GROUP_CASE_INFO_ID,
				'name' => 'Dictated by',
				'type' => self::FIELD_TYPE_USER,
				'sort' => 16,
				'show' => 'users',
			],
			'procedure' => [
				'group_id' => self::GROUP_DESCRIPTIONS_ID,
				'name' => 'Procedure',
				'type' => self::FIELD_TYPE_CASE_TYPE,
				'sort' => 1,
				'show' => 'input',
			],
			'pre_op_diagnosis' => [
				'group_id' => self::GROUP_DESCRIPTIONS_ID,
				'name' => 'Pre-Op Diagnosis',
				'type' => self::FIELD_TYPE_DIAGNOSIS,
				'sort' => 2,
				'show' => 'input',
			],
			'operation_time' => [
				'group_id' => self::GROUP_CONCLUSIONS_ID,
				'name' => 'Operation Start / Finish Time',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 2,
				'show' => 'input',
			],
			'post_op_diagnosis' => [
				'group_id' => self::GROUP_DESCRIPTIONS_ID,
				'name' => 'Post-Op Diagnosis',
				'type' => self::FIELD_TYPE_DIAGNOSIS,
				'sort' => 3,
				'show' => 'input',
			],
			'specimens_removed' => [
				'group_id' => self::GROUP_FOLLOW_UP_ID,
				'name' => 'Specimens Removed',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 1,
				'show' => 'textarea',
			],
			'anesthesia_administered' => [
				'group_id' => self::GROUP_DESCRIPTIONS_ID,
				'name' => 'Anesthesia Administered',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 6,
				'show' => 'input',
			],
			'ebl' => [
				'group_id' => self::GROUP_MATERIALS_ID,
				'name' => 'EBL',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 2,
				'show' => 'input',
			],
			'blood_transfused' => [
				'group_id' => self::GROUP_MATERIALS_ID,
				'name' => 'Blood Transfused',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 3,
				'show' => 'input',
			],
			'fluids' => [
				'group_id' => self::GROUP_MATERIALS_ID,
				'name' => 'Fluids',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 4,
				'show' => 'input',
			],
			'drains' => [
				'group_id' => self::GROUP_MATERIALS_ID,
				'name' => 'Drains',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 5,
				'show' => 'input',
			],
			'urine_output' => [
				'group_id' => self::GROUP_MATERIALS_ID,
				'name' => 'Urine Output',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 6,
				'show' => 'input',
			],
			'total_tourniquet_time' => [
				'group_id' => self::GROUP_MATERIALS_ID,
				'name' => 'Total Tourniquet Time',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 1,
				'show' => 'input',
			],
			'consent' => [
				'group_id' => self::GROUP_DESCRIPTIONS_ID,
				'name' => 'Consent',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 4,
				'show' => 'textarea',
			],
			'complications' => [
				'group_id' => self::GROUP_DESCRIPTIONS_ID,
				'name' => 'Complications',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 9,
				'show' => 'textarea',
			],
			'clinical_history' => [
				'group_id' => self::GROUP_DESCRIPTIONS_ID,
				'name' => 'Clinical History & Indications for Procedure',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 5,
				'show' => 'textarea',
			],
			'approach' => [
				'group_id' => self::GROUP_DESCRIPTIONS_ID,
				'name' => 'Approach',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 7,
				'show' => 'textarea',
			],
			'findings' => [
				'group_id' => self::GROUP_CONCLUSIONS_ID,
				'name' => 'Findings',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 1,
				'show' => 'textarea',
			],
			'description_procedure' => [
				'group_id' => self::GROUP_DESCRIPTIONS_ID,
				'name' => 'Description of Procedure',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 8,
				'show' => 'textarea',
			],
			'follow_up_care' => [
				'group_id' => self::GROUP_FOLLOW_UP_ID,
				'name' => 'Follow Up Care',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 2,
				'show' => 'textarea',
			],
			'conditions_for_discharge' => [
				'group_id' => self::GROUP_FOLLOW_UP_ID,
				'name' => 'Conditions for Discharge',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 3,
				'show' => 'textarea',
			],
			'scribe' => [
				'group_id' => self::GROUP_DESCRIPTIONS_ID,
				'name' => 'Scribe',
				'type' => self::FIELD_TYPE_TEXT,
				'sort' => 4,
				'show' => 'textarea',
			],
		];
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
		return self::getPropField($field, 'sort');
	}

	public static function getShowByField($field)
	{
		return self::getPropField($field, 'show');
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

	public function toArray()
	{
		$data = parent::toArray();
		$data['active'] = (bool)$this->active;
		$data['type'] = self::getTypeByField($this->field);
		$data['show'] = self::getShowByField($this->field);
		$data['group_id'] = (int)$this->group_id;
		if($this->field === 'custom') {
			$data['name'] = $this->name;
			$data['type'] = 'text';
		} else {
			$data['name'] = self::getNameByField($this->field);
		}
		return $data;
	}
}
