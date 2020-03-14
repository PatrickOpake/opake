<?php

namespace Opake\ActivityLogger\Action\Billing;

use Opake\ActivityLogger\Action\ModelAction;

class ClaimSentAction extends ModelAction
{

	protected function getSearchParams()
	{
		/** @var \Opake\Model\Billing\Navicure\Claim $claim */
		$claim = $this->getExtractor()->getModel();
		return [
			'case_id' => $claim->case_id,
		];
	}

	/**
	 * @return array
	 */
	protected function fetchDetails()
	{
		/** @var \Opake\Model\Billing\Navicure\Claim $claim */
		$claim = $this->getExtractor()->getModel();
		$procedures = [];
		$totalAmount = 0;
		foreach ($claim->case->coding->bills->find_all() as $bill) {
			if ($bill->amount) {
				$totalAmount += $bill->amount;
			}
			$entry = $bill->getChargeMasterEntry();
			if ($entry) {
				$procedures[] = $entry->cpt;
			}
		}

		return [
			'claim_insurance' => $claim->primary_insurance_id,
			'patient' => $claim->case->registration->patient_id,
			'total_amount' => $totalAmount,
		    'procedures' => $procedures
		];
	}
}