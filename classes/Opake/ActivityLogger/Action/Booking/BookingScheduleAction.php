<?php

namespace Opake\ActivityLogger\Action\Booking;

use Opake\ActivityLogger\Action\ModelAction;

class BookingScheduleAction extends ModelAction
{
	protected function fetchDetails()
	{
		$booking = $this->getExtractor()->getModel();

		return [
			'booking_id' => $booking->id(),
		];
	}

	protected function getSearchParams()
	{
		$case = $this->getExtractor()->getAdditionalInfo('case');
		return [
			'case_id' => $case->id()
		];
	}
}