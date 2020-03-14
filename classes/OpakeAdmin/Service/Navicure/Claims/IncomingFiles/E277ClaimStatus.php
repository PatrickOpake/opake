<?php

namespace OpakeAdmin\Service\Navicure\Claims\IncomingFiles;

use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Billing\Navicure\Claim\StatusAcknowledgment;
use OpakeAdmin\Service\ASCX12\AbstractParser;
use OpakeAdmin\Service\ASCX12\E277\Response\Segments\PatientDetails;
use OpakeAdmin\Service\ASCX12\E277\Response\Segments\PatientDetails\ClaimLevelStatus;
use OpakeAdmin\Service\ASCX12\E277\Response\Segments\PatientDetails\ClaimStatusTracking;
use OpakeAdmin\Service\ASCX12\E277\Response\Segments\PatientDetails\ServiceLevelStatus;
use OpakeAdmin\Service\ASCX12\E277\Response\Segments\PatientDetails\ServiceStatusTracking;

class E277ClaimStatus extends AbstractIncomingFile
{

	public function getParser()
	{
		return new \OpakeAdmin\Service\ASCX12\E277\Response\AcknowledgmentParser();
	}

	/**
	 * @return int
	 */
	public function getTransactionId()
	{
		return \Opake\Model\Billing\Navicure\Log::TRANSACTION_277;
	}

	/**
	 * @param \OpakeAdmin\Service\ASCX12\AbstractResponseSegment $rootSegment
	 * @param \Opake\Model\Billing\Navicure\Log $logRecord
	 * @throws \Exception
	 */
	public function handle($rootSegment, $logRecord)
	{
		$app = \Opake\Application::get();
		try {
			$app->db->begin_transaction();

			$hlRoot = $rootSegment->getFirstChildSegment()
				->getFirstChildSegment()
				->getFirstChildSegment();

			foreach ($hlRoot->getChildSegments() as $segment) {
				if ($segment instanceof PatientDetails) {
					/** @var ClaimStatusTracking $statusTrackingSegment */
					$childSegments = $segment->getChildSegments();
					$statusTrackingSegment = $childSegments[1];
					$claimId = $statusTrackingSegment->getReferenceIdentifier();

					$claim = $app->orm->get('Billing_Navicure_Claim', (int) $claimId);
					if ($claim->loaded()) {
						$isRejected = (bool) $statusTrackingSegment->getClaimLevelStatuses();
						$app->orm->get('Billing_Navicure_Claim_StatusAcknowledgment')
							->where('claim_id', $claim->id())
							->delete_all();
						$app->orm->get('Billing_Navicure_Claim_StatusAcknowledgmentService')
							->where('claim_id', $claim->id())
							->delete_all();

						/** @var ClaimLevelStatus $claimLevelStatusSegment */
						foreach ($statusTrackingSegment->getClaimLevelStatuses() as $claimLevelStatusSegment) {
							if ($claimLevelStatusSegment->isAmountAccepted()) {
								$isRejected = false;
							}

							$acknowledgment = $app->orm->get('Billing_Navicure_Claim_StatusAcknowledgment');
							$acknowledgment->claim_id = $claim->id();
							$acknowledgment->date = TimeFormat::formatToDB($claimLevelStatusSegment->getDate());
							$acknowledgment->amount = $claimLevelStatusSegment->getAmount();
							$acknowledgment->status = $claimLevelStatusSegment->isAmountAccepted() ? StatusAcknowledgment::STATUS_ACCEPTED : StatusAcknowledgment::STATUS_REJECTED;
							$acknowledgment->note = $claimLevelStatusSegment->getNote();
							$acknowledgment->save();
						}

						/** @var ServiceStatusTracking $serviceTrackingSegment */
						foreach ($statusTrackingSegment->getChildSegments() as $serviceTrackingSegment) {
							if ($serviceTrackingSegment->getServiceLevelStatuses()) {
								/** @var ServiceLevelStatus $serviceLevelStatus */
								foreach ($serviceTrackingSegment->getServiceLevelStatuses() as $serviceLevelStatus) {
									$acknowledgment = $app->orm->get('Billing_Navicure_Claim_StatusAcknowledgmentService');
									$acknowledgment->claim_id = $claim->id();
									$acknowledgment->date = TimeFormat::formatToDB($serviceLevelStatus->getDate());
									$acknowledgment->amount = $serviceTrackingSegment->getAmount();
									$acknowledgment->service_code = $serviceTrackingSegment->getCode();
									$acknowledgment->status = $serviceLevelStatus->isAmountAccepted() ? StatusAcknowledgment::STATUS_ACCEPTED : StatusAcknowledgment::STATUS_REJECTED;
									$acknowledgment->note = $serviceLevelStatus->getNote();
									$acknowledgment->save();
								}
							}
						}

						if ($isRejected) {
							if ($claim->status == Claim::STATUS_ACCEPTED_BY_PROVIDER || $claim->status == Claim::STATUS_SENT) {
								$claim->status = Claim::STATUS_REJECTED_BY_PROVIDER;
							}
							if ($claim->status == Claim::STATUS_ACCEPTED_BY_PAYER) {
								$claim->status = Claim::STATUS_REJECTED_BY_PAYER;
							}
							$claim->additional_status = 0;
						}

						$claim->last_transaction_date = TimeFormat::formatToDBDatetime(new \DateTime());

						$claim->save();

						$logRecord->claim_id = $claim->id();
						$logRecord->save();
					}
				}
			}

			$app->db->commit();
		} catch (\Exception $e) {
			$app->db->rollback();
			throw $e;
		}
	}
}