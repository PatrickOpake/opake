<?php

namespace Opake\Model\Booking;

use Opake\Model\Insurance\AbstractType;

class PatientInsurance extends AbstractType
{
	public $id_field = 'id';
	public $table = 'booking_patient_insurance_types';
	protected $_row = [
		'id' => null,
		'booking_patient_id' => null,
		'type' => null,
		'order' => null,
		'selected_insurance_id' => null,
		'insurance_data_id' => null
	];

	public function toArray()
	{
		$insuranceDataModel = $this->getInsuranceDataModel();

		return [
			'id' => $this->id(),
			'type' => $this->type,
			'order' => $this->order,
			'title' => $this->getTitle(),
			'selected_insurance_id' => $this->selected_insurance_id,
			'is_patient_insurance' => false,
			'data' => ($insuranceDataModel->loaded()) ? $insuranceDataModel->toArray() : null
		];
	}
}