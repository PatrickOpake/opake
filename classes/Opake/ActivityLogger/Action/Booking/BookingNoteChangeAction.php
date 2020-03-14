<?php

namespace Opake\ActivityLogger\Action\Booking;

use Opake\ActivityLogger\Action\ModelAction;

class BookingNoteChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		$booking = $model->booking;

		return [
			'booking_id' => $booking->id()
		];
	}

	protected function getFieldsForCompare()
	{
		return [
			'text'
		];
	}
}