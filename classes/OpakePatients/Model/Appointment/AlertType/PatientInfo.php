<?php

namespace OpakePatients\Model\Appointment\AlertType;

use Opake\Helper\TimeFormat;

class PatientInfo extends AbstractType
{
	protected function getHiddenKey()
	{
		return 'is_info_alert_hidden';
	}

	protected function getConfirmKey()
	{
		return 'is_patient_info_confirmed';
	}

	protected function getType()
	{
		return AbstractType::TYPE_PATIENT_INFO;
	}

	protected function getLink()
	{
		return [
			'state' => 'app.view-appointment.info',
			'params' => [
				'appointment' => $this->registration->id()
			]
		];
	}

	protected function getLabel()
	{
		$caseStartDate = TimeFormat::fromDBDatetime($this->registration->case->time_start);
		$caseStartDateFormatted = $caseStartDate->format('m/d/Y');

		return 'Confirm your personal info for case on ' . $caseStartDateFormatted;
	}

}