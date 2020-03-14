<?php

namespace OpakeAdmin\Service\Navicure\Claims\IncomingFiles;

use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;
use OpakeAdmin\Service\ASCX12\AbstractParser;
use OpakeAdmin\Service\ASCX12\E997\Response\Headers\AkTransactionSetHeader;

class E997Acknowledgment extends AbstractIncomingFile
{

	/**
	 * @return AbstractParser
	 */
	public function getParser()
	{
		return new \OpakeAdmin\Service\ASCX12\E997\Response\AcknowledgmentParser();
	}

	/**
	 * @return int
	 */
	public function getTransactionId()
	{
		return \Opake\Model\Billing\Navicure\Log::TRANSACTION_997;
	}

	/**
	 * @param \OpakeAdmin\Service\ASCX12\AbstractResponseSegment $rootSegment
	 * @param \Opake\Model\Billing\Navicure\Log $logRecord
	 * @throws \Exception
	 */
	public function handle($rootSegment, $logRecord)
	{
		$app = \Opake\Application::get();
		if (!$logRecord) {
			throw new \Exception('Empty root segment');
		}

		$tsHeadersParent = $rootSegment->getFirstChildSegment()
			->getFirstChildSegment()
			->getFirstChildSegment();

		/** @var AkTransactionSetHeader $tsHeader */
		foreach ($tsHeadersParent->getChildSegments() as $tsHeader) {
			$transactionControlNumber = $tsHeader->getTransactionControlNumber();
			$claim = $app->orm->get('Billing_Navicure_Claim', (int) $transactionControlNumber);
			if (!$claim->loaded()) {
				throw new \Exception('Claim with ID . ' . $transactionControlNumber . ' is not found');
			}
			$logRecord->claim_id = $claim->id();
			$logRecord->save();

			if ($claim->status == Claim::STATUS_SENT) {
				if ($tsHeader->hasAnyAcceptedStatus()) {
					$claim->status = Claim::STATUS_ACCEPTED_BY_PROVIDER;
					if ($tsHeader->isAcceptedWithErrors()) {
						$claim->additional_status = Claim::ADD_STATUS_ACCEPTED_WITH_ERRORS;
					}
				}
				if ($tsHeader->hasAnyRejectedStatus()) {
					$claim->status = Claim::STATUS_REJECTED_BY_PROVIDER;
					if ($tsHeader->isRejectedAssurenceValidityTestFailed()) {
						$claim->additional_status = Claim::ADD_STATUS_REJECTED_ASSURANCE_FAILED;
					}
					if ($tsHeader->isRejectedMacFailed()) {
						$claim->additional_status = Claim::ADD_STATUS_REJECTED_MAC_FAILED;
					}
					if ($tsHeader->isRejectedContentCantBeAnalyzed()) {
						$claim->additional_status = Claim::ADD_STATUS_REJECTED_CONTENT_COULDNT_BE_ANALYZED;
					}
					$firstError = $tsHeader->getFirstChildSegment();
					if ($firstError) {
						$claim->error = $this->makeErrorString($firstError);
					}
				}
			} else if ($claim->status == Claim::STATUS_ACCEPTED_BY_PROVIDER) {
				if ($tsHeader->hasAnyAcceptedStatus()) {
					$claim->status = Claim::STATUS_ACCEPTED_BY_PAYER;
					if ($tsHeader->isAcceptedWithErrors()) {
						$claim->additional_status = Claim::ADD_STATUS_ACCEPTED_WITH_ERRORS;
					}
				}
				if ($tsHeader->hasAnyRejectedStatus()) {
					$claim->status = Claim::STATUS_ACCEPTED_BY_PAYER;
					if ($tsHeader->isRejectedAssurenceValidityTestFailed()) {
						$claim->additional_status = Claim::ADD_STATUS_REJECTED_ASSURANCE_FAILED;
					}
					if ($tsHeader->isRejectedMacFailed()) {
						$claim->additional_status = Claim::ADD_STATUS_REJECTED_MAC_FAILED;
					}
					if ($tsHeader->isRejectedContentCantBeAnalyzed()) {
						$claim->additional_status = Claim::ADD_STATUS_REJECTED_CONTENT_COULDNT_BE_ANALYZED;
					}
					$firstError = $tsHeader->getFirstChildSegment();
					if ($firstError) {
						$claim->error = $this->makeErrorString($firstError);
					}
				}
			}

			$claim->last_transaction_date = TimeFormat::formatToDBDatetime(new \DateTime());

			$claim->save();
		}
	}


	protected function makeErrorString($errorSegment)
	{
		return 'Error in segment [' . $errorSegment->getErrorSegmentDefinition() . ':' .
			$errorSegment->getErrorSegmentPosition() . ', Loop: ' . $errorSegment->getErrorSegmentLoopId() . ']: '
			. $errorSegment->getErrorDescription();
	}

}