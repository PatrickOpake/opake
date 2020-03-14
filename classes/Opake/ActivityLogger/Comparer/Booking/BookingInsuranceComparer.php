<?php

namespace Opake\ActivityLogger\Comparer\Booking;

use Opake\ActivityLogger\Comparer\ChildModelsChangesComparer;

class BookingInsuranceComparer extends ChildModelsChangesComparer
{
	protected function getChildModelFields()
	{

		return [
			'insurances' => [
				'comparerClass' => '\Opake\ActivityLogger\Comparer\Booking\BookingInsuranceComparer',
			]
		];
	}
}