<?php

namespace Opake\Model\Patient\Appointment\Form;

use Opake\Model\AbstractModel;

class Influenza extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'patient_appointment_form_influenza';
	protected $_row = [
		'id' => null,
		'case_registration_id' => null,
		'filled_date' => null,
		'travel_outside' => null,
		'flu_vaccine' => null,
		'flu_vaccine_month' => null,
		'travel_outside_date' => null,
		'illnesses' => null
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
		$data['flu_vaccine_month'] = ($data['flu_vaccine_month'] !== null) ? (int) $data['flu_vaccine_month'] : null;
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
			'illnesses'
		];
	}

	public function getIllnesses()
	{
		if (is_string($this->illnesses)) {
			return unserialize($this->illnesses);
		}
		return $this->illnesses;
	}

	public static function getIllnessesFields()
	{
		return [
			[
				[
					'name' => 'fever_or_chills',
					'label' => 'Fever or Chills'
				],
				[
					'name' => 'muscle_pain',
					'label' => 'Muscle Pain'
				]
			],
			[
				[
					'name' => 'cough',
					'label' => 'Cough'
				],
				[
					'name' => 'watery_diarrhea',
					'label' => 'Watery Diarrhea'
				]
			],
			[
				[
					'name' => 'difficulty_breathing',
					'label' => 'Difficulty Breathing'
				],
				[
					'name' => 'vomiting',
					'label' => 'Vomiting'
				]
			],
			[
				[
					'name' => 'chest_discomfort',
					'label' => 'Chest Discomfort'
				],
				[
					'name' => 'extreme_exhaustion',
					'label' => 'Extreme Exhaustion'
				]
			],
			[
				[
					'name' => 'sore_throat',
					'label' => 'Sore Throat'
				],
				[
					'name' => 'stuffy_nose',
					'label' => 'Stuffy Nose'
				]
			],
			[
				[
					'name' => 'headache',
					'label' => 'Headache'
				],
				[
					'name' => 'sneezing',
					'label' => 'Sneezing'
				]
			]
		];
	}

}