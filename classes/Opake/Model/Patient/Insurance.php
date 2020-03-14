<?php

namespace Opake\Model\Patient;

use Opake\Model\Insurance\AbstractType;

class Insurance extends AbstractType
{
	public $id_field = 'id';
	public $table = 'patient_insurance_types';
	protected $_row = [
		'id' => null,
		'patient_id' => null,
		'type' => null,
		'order' => null,
		'insurance_data_id' => null
	];

	protected $formatters = [
		'LedgerInsurancesList' => [
			'class' => '\Opake\Formatter\Billing\Ledger\Patient\PatientInsuranceFormatter'
		]
	];

	public function fromBookingPatientInsurance(\Opake\Model\Booking\PatientInsurance $insurance)
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
			'is_patient_insurance' => true,
			'data' => ($insuranceDataModel->loaded()) ? $insuranceDataModel->toArray() : null
		];
	}
}
