<?php

namespace OpakeAdmin\Service\Navicure\Claims\Generator;

use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Billing\Navicure\Log;
use OpakeAdmin\Service\Navicure\Claims\ClaimGenerator;
use OpakeAdmin\Service\Navicure\Claims\SFTP\Agent;

class InstitutionalClaimGenerator extends ClaimGenerator
{
	public function getCaseErrors()
	{
		$checker = new \OpakeAdmin\Service\ASCX12\E837I\Request\CaseClaimErrorChecker($this->case);
		return $checker->checkErrors();
	}

	public function tryToSendClaim()
	{
		$app = \Opake\Application::get();

		if (!$this->case->coding->loaded()) {
			throw new \Exception('Case has no saved coding');
		}

		$requestDateTime = new \DateTime();

		/** @var \Opake\Model\Billing\Navicure\Claim $newClaim */
		$newClaim = $app->orm->get('Billing_Navicure_Claim');
		$newClaim->case_id = $this->case->id();
		$newClaim->active = 0;
		$newClaim->status = Claim::STATUS_NEW;
		$newClaim->last_transaction_date = TimeFormat::formatToDBDatetime($requestDateTime);
		$newClaim->sending_date = TimeFormat::formatToDBDatetime($requestDateTime);
		$newClaim->type = Claim::TYPE_ELECTRONIC_UB04_CLAIM;
		$newClaim->save();

		try {

			$claimGen = new \OpakeAdmin\Service\ASCX12\E837I\Request\CaseClaimGenerator($this->case, $newClaim, $this->collectionOfBills);
			$content = $claimGen->generateContent();

			$disableSending = false;
			if ($app->config->has('app.navicure_api.sftp.disable_sending')) {
				if ($app->config->get('app.navicure_api.sftp.disable_sending')) {
					$disableSending = true;
				}
			}

			if (!$disableSending) {
				$agent = $this->createNewAgent();
				$agent->putNewClaim($content);
			}

		} catch (\Exception $e) {
			/* hack for phpseclib issue */
			$newClaim->conn = $app->db->refresh_connection();
			$newClaim->delete();
			throw $e;
		}

		/* hack for phpseclib issue */
		$newClaim->conn = $app->db->refresh_connection();

		$newClaim->active = 1;
		$newClaim->last_update = TimeFormat::formatToDBDatetime($requestDateTime);
		$newClaim->status = Claim::STATUS_SENT;
		$newClaim->first_name = $this->case->registration->first_name;
		$newClaim->last_name = $this->case->registration->last_name;
		$newClaim->mrn = $this->case->registration->patient->getFullMrn();
		$newClaim->dos = $this->case->time_start;
		if (!$this->case->coding->isPrimaryInsuranceAssigned()) {
			$codingInsurance = $this->case->coding->getAssignedInsurance();
		} else {
			$codingInsurance = $this->case->coding->getPrimaryInsurance();
		}
		if ($codingInsurance && $codingInsurance->getCaseInsurance() && $codingInsurance->getCaseInsurance()->isRegularInsurance()) {
			$newClaim->insurance_payer_id = $codingInsurance->getCaseInsurance()->getInsuranceDataModel()->insurance->id();
		}
		$newClaim->copyPrimaryInsurance();
		$newClaim->save();

		$logRecord = $app->orm->get('Billing_Navicure_Log');
		$logRecord->claim_id = $newClaim->id();
		$logRecord->transaction = Log::TRANSACTION_837I;
		$logRecord->direction = Log::DIRECTION_OUT;
		$logRecord->time = TimeFormat::formatToDBDatetime($requestDateTime);
		$logRecord->data = $content;
		$logRecord->save();

		return $newClaim;
	}

	public function hasUnresolvedClaim()
	{
		return $this->hasUnresolvedClaimForType(Claim::TYPE_ELECTRONIC_UB04_CLAIM);
	}
}