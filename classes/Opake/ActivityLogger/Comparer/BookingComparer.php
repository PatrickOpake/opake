<?php

namespace Opake\ActivityLogger\Comparer;


class BookingComparer extends ChildModelsChangesComparer
{
	/**
	 * @return array
	 */
	protected function getChildModelFields()
	{

		return [
			'insurances' => [
				'comparerClass' => '\Opake\ActivityLogger\Comparer\Booking\BookingInsuranceComparer',
			    'extractorClass' => '\Opake\ActivityLogger\Extractor\Booking\BookingInsuranceExtractor',
				'ignoreFields' => [
					'id',
					'booking_id',
					'booking_patient_id',
					'selected_insurance_id',
				    'insurance_data_id',
				    'insurance_verified',
				    'is_pre_authorization_completed'
				]
			]
		];
	}
}