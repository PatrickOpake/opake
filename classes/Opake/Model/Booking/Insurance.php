<?php

namespace Opake\Model\Booking;

use Opake\Model\Insurance\AbstractType;

class Insurance extends AbstractType
{
	public $id_field = 'id';
	public $table = 'booking_insurance_types';
	protected $_row = [
		'id' => null,
		'booking_id' => null,
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
			'order' => (!empty($this->order)) ? (int) $this->order : null,
			'title' => $this->getTitle(),
			'selected_insurance_id' => $this->selected_insurance_id,
			'is_patient_insurance' => false,
			'data' => ($insuranceDataModel->loaded()) ? $insuranceDataModel->toArray() : null
		];
	}

	public static function getPrimaryInsurancesList()
	{
		return [
			1 => 'Primary',
			2 => 'Secondary',
			3 => 'Tertiary',
			4 => 'Quaternary',
			5 => 'Other'
		];
	}
}
