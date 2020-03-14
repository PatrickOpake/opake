<?php

namespace Opake\Model\Cases\Registration;

use Opake\Model\Insurance\AbstractType;

class Insurance extends AbstractType
{
	public $id_field = 'id';
	public $table = 'case_registration_insurance_types';
	protected $_row = [
		'id' => null,
		'registration_id' => null,
		'type' => null,
		'order' => null,
		'selected_insurance_id' => null,
		'insurance_data_id' => null,
	    'deleted' => false
	];

	protected $has_one = [
		'verification' => [
			'model' => 'Cases_Registration_Insurance_Verification',
			'key' => 'case_insurance_id',
			'cascade_delete' => true
		],
	];

	protected $formatters = [
		'ListEntry' => [
			'class' => 'Opake\Formatter\Billing\Navicure\Eligibility\ListEntryFormatter'
		]
	];

	public function fromPatientInsurance(\Opake\Model\Patient\Insurance $insurance)
	{
		$this->type = $insurance->type;
		$this->order = $insurance->order;

		$this->getInsuranceDataModel()->fromBaseInsurance($insurance->getInsuranceDataModel());
	}

	public function toArray()
	{
		$insuranceDataModel = $this->getInsuranceDataModel();

		return [
			'id' => $this->id(),
			'type' => $this->type,
			'order' => (!empty($this->order)) ? (int) $this->order : null,
			'title' => $this->getTitle(),
			'selected_insurance_id' => $this->selected_insurance_id,
			'is_patient_insurance' => false,
			'data' => ($insuranceDataModel->loaded()) ? $insuranceDataModel->toArray() : null,
			'verification' => $this->verification->toArray(),
		    'deleted' => $this->deleted
		];
	}
}
