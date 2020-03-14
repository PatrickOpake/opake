<?php

namespace OpakePatients\Model\Appointment\AlertType;

use Opake\Helper\TimeFormat;

class Forms extends AbstractType
{
	protected function getHiddenKey()
	{
		return 'is_forms_alert_hidden';
	}

	protected function getConfirmKey()
	{
		return 'is_forms_confirmed';
	}

	protected function getType()
	{
		return AbstractType::TYPE_FORMS;
	}

	protected function getLink()
	{
		return [
			'state' => 'app.view-appointment',
			'params' => [
				'appointment' => $this->registration->id()
			]
		];
	}

	protected function getLabel()
	{
		$caseStartDate = TimeFormat::fromDBDatetime($this->registration->case->time_start);
		$caseStartDateFormatted = $caseStartDate->format('m/d/Y');

		return 'Complete your forms for case on ' . $caseStartDateFormatted;
	}

}