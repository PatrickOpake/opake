<?php

namespace OpakePatients\Model\Appointment\AlertType;

use Opake\Helper\TimeFormat;

class Insurances extends AbstractType
{
	protected function getHiddenKey()
	{
		return 'is_insurance_alert_hidden';
	}

	protected function getConfirmKey()
	{
		return 'is_insurances_confirmed';
	}

	protected function getType()
	{
		return AbstractType::TYPE_INSURANCES;
	}

	protected function getLink()
	{
		return [
			'state' => 'app.view-appointment.insurance',
			'params' => [
				'appointment' => $this->registration->id()
			]
		];
	}

	protected function getLabel()
	{
		$caseStartDate = TimeFormat::fromDBDatetime($this->registration->case->time_start);
		$caseStartDateFormatted = $caseStartDate->format('m/d/Y');

		return 'Confirm your insurance info for case on ' . $caseStartDateFormatted;
	}

}