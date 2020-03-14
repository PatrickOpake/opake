<?php

namespace Opake\Model\Patient\Appointment\Form;

use Opake\Model\AbstractModel;
use Opake\Model\Cases\Registration\Reconciliation;

class PreOperative extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'patient_appointment_form_pre_operative';
	protected $_row = [
		'id' => null,
		'case_registration_id' => null,
		'height_ft' => null,
		'height_in' => null,
		'weight_lbs' => null,
		'medications' => null,
		'steroids' => null,
		'allergies' => null,
		'allergic_to_latex' => null,
		'allergic_to_latex_reason' => null,
		'allergic_to_food' => null,
		'allergic_to_food_reason' => null,
		'allergic_other' => null,
		'allergic_other_reason' => null,
		'conditions' => null,
		'surgeries_hospitalizations' => null,
		'family_problems' => null,
		'family_anesthesia_problems' => null,
		'smoke' => null,
		'smoke_how_long_yrs' => null,
		'smoke_packs_per_day' => null,
		'smoke_description' => null,
		'drink' => null,
		'drink_how_long_yrs' => null,
		'drink_drinks_per_week' => null,
		'drink_description' => null,
		'travel_outside' => null,
		'living_will' => null,
		'primary_care_name' => null,
		'primary_care_phone' => null,
		'transportation_name' => null,
		'transportation_phone' => null,
		'caretaker_name' => null,
		'caretaker_phone' => null,
		'leave_message' => null,
		'leave_message_phone' => null
	];

	protected $belongs_to = [
		'case_registration' => [
			'model' => 'Cases_Registration',
			'key' => 'case_registration_id',
		]
	];



	public function save()
	{
		foreach ($this->getSerializedFields() as $fieldName) {
			if (!is_null($this->{$fieldName}) && !is_string($this->{$fieldName})) {
				$this->{$fieldName} = serialize($this->{$fieldName});
			}
		}

		parent::save();
	}

	public function toArray()
	{
		$data = parent::toArray();

		foreach ($this->getSerializedFields() as $fieldName) {
			if (is_string($data[$fieldName])) {
				$data[$fieldName] = unserialize($data[$fieldName]);
			}
		}

		return $data;
	}

	protected function getSerializedFields()
	{
		return [
			'medications',
			'allergies',
			'surgeries_hospitalizations',
			'family_problems',
			'family_anesthesia_problems',
			'travel_outside',
			'conditions'
		];
	}
}