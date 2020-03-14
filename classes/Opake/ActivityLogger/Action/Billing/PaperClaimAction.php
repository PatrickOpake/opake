<?php

namespace Opake\ActivityLogger\Action\Billing;

use Opake\ActivityLogger\Action\ArrayAction;

class PaperClaimAction extends ArrayAction
{

	protected function fetchDetails()
	{
		$mainArray = $this->getExtractor()->getArray();
		$caseIds = [];
		$patients = [];
		foreach ($mainArray['cases'] as $caseId) {
			$caseIds[] = $caseId;
		}

		foreach ($mainArray['patients'] as $patient) {
			$patients[] = $patient;
		}

		$details = [];
		if ($caseIds) {
			$details['case_ids'] = $caseIds;
			$details['patients'] = $patients;
		}

		return $details;
	}
}