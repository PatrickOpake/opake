<?php

namespace Opake\ActivityLogger\Action\Booking;

use Opake\ActivityLogger\Action\ArrayAction;

class BookingPrintAction extends ArrayAction
{
	protected function fetchDetails()
	{
		$bookingIds = [];
		foreach ($this->getExtractor()->getArray() as $bookingId) {
			$bookingIds[] = $bookingId;
		}

		$details = [];
		if ($bookingIds) {
			$details['booking_ids'] = $bookingIds;
		}

		return $details;
	}
}