<?php

namespace Opake\ActivityLogger\Formatter\Settings\OperativeReportTemplates;

use Opake\ActivityLogger\DefaultFormatter;

class ChangesFormatter extends DefaultFormatter
{

	protected function getLabels()
	{
		return [
			'name' => 'Name',
			'cpt_id' => 'Procedure',
			'ebl' => 'EBL',
			'drains' => 'Drains',
			'consent' => 'Consent',
			'complications' => 'Complications',
			'approach' => 'Approach',
			'description_procedure' => 'Description of Procedure',
			'follow_up_care' => 'Follow Up Care',
			'conditions_for_discharge' => 'Conditions for Discharge',
			'scribe' => 'Scribe',
			'anesthesia_administered' => 'Anesthesia Administered'
		];
	}
}