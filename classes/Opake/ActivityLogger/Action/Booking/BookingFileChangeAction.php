<?php

namespace Opake\ActivityLogger\Action\Booking;

use Opake\ActivityLogger\Action\ModelAction;

class BookingFileChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$booking = $this->getExtractor()->getAdditionalInfo('booking');
		$chart =  $this->getExtractor()->getModel();

		$details = [];
		if ($booking && $booking->loaded()) {
			$details['booking_id'] = $booking->id();
		}

		$details['chart_id'] = $chart->id();

		return $details;
	}

	protected function getFieldsForCompare()
	{
		return [
			'name',
		];
	}
}