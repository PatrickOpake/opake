<?php

namespace OpakeAdmin\Service\Navicure\Claims\IncomingFiles;

use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Ledger\PaymentInfo;
use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Billing\Navicure\Payment;
use OpakeAdmin\Service\ASCX12\AbstractParser;
use OpakeAdmin\Service\ASCX12\E835\Response\Segments\ClaimPaymentInformation;
use OpakeAdmin\Service\ASCX12\E835\Response\Segments\ClaimPaymentInformation\ServiceInformation;
use OpakeAdmin\Service\ASCX12\E835\Response\Segments\DetailSummary;
use OpakeAdmin\Service\ASCX12\E835\Response\Segments\FinancialInformation;

class E835ClaimPayment extends AbstractIncomingFile
{

	protected $skipFirstBunch = false;

	/**
	 * @todo: remove
	 */
	public function setSkipFirstBunch($skipFirst)
	{
		$this->skipFirstBunch = $skipFirst;
	}

	/**
	 * @return AbstractParser
	 */
	public function getParser()
	{
		return new \OpakeAdmin\Service\ASCX12\E835\Response\ClaimPaymentParser();
	}

	/**
	 * @return int
	 */
	public function getTransactionId()
	{
		return \Opake\Model\Billing\Navicure\Log::TRANSACTION_835;
	}

	/**
	 * @param \OpakeAdmin\Service\ASCX12\AbstractResponseSegment $rootSegment
	 * @param \Opake\Model\Billing\Navicure\Log $logRecord
	 * @throws \Exception
	 */
	public function handle($rootSegment, $logRecord)
	{
		$this->handleClaims($rootSegment, $logRecord);
		$this->handlePaymentsBunch($rootSegment);
	}

	/**
	 * @param \OpakeAdmin\Service\ASCX12\AbstractResponseSegment $rootSegment
	 * @param \Opake\Model\Billing\Navicure\Log $logRecord
	 * @throws \Exception
	 */
	protected function handleClaims($rootSegment, $logRecord)
	{

		$app = \Opake\Application::get();
		$gs = $rootSegment->getFirstChildSegment();

		$usedClaimId = null;
		$errors = '';

		$claimPaymentNum = 0;
		foreach ($gs->getChildSegments() as $st) {
			$claimPaymentNum++;
			if ($this->skipFirstBunch && $claimPaymentNum == 1) {
				continue;
			}
			foreach ($st->getChildSegments() as $stChildSegment) {
				if ($stChildSegment instanceof DetailSummary) {
					foreach ($stChildSegment->getChildSegments() as $segment) {
						if ($segment instanceof ClaimPaymentInformation) {

							$claimId = $segment->getReferenceIdentifier();

							/** @var \Opake\Model\Billing\Navicure\Claim $claim */
							$claim = $app->orm->get('Billing_Navicure_Claim', (int) $claimId);

							if (!$claim->loaded()) {
								$errors .= 'Claim with ID . ' . $claimId . ' is not found. ';
								$app->logger->warning('835 Handling: Claim with ID . ' . $claimId . ' is not found');
								continue;
							}

							$claim->additional_status = 0;
							if ($segment->isPaymentDenied()) {
								$claim->status = Claim::STATUS_PAYMENT_DENIED;
							} else if ($segment->isPaymentProcessed()) {
								$claim->status = Claim::STATUS_PAYMENT_PROCESSED;
							}

							$claim->last_transaction_date = TimeFormat::formatToDBDatetime(new \DateTime());

							$claim->save();

							$usedClaimId = $claim->id();
						}
					}
				}
			}
		}

		$logRecord->claim_id = $usedClaimId;
		$logRecord->error = $errors;
		$logRecord->save();
	}

	/**
	 * Handle payments for "Claims Processing" tab
	 *
	 * @param \OpakeAdmin\Service\ASCX12\AbstractResponseSegment $rootSegment
	 * @throws \Exception
	 */
	protected function handlePaymentsBunch($rootSegment)
	{
		$app = \Opake\Application::get();

		$app->db->begin_transaction();
		try {

			$gs = $rootSegment->getFirstChildSegment();

			$claimPaymentNum = 0;
			foreach ($gs->getChildSegments() as $st) {

				$claimPaymentNum++;
				if ($this->skipFirstBunch && $claimPaymentNum == 1) {
					continue;
				}

				$bunchPayerId = null;
				$bunchOrganizationId = null;

				$bunchAmountPaid = 0.0;
				$bunchTotalAmount = 0.0;
				$bunchPatientResponsible = 0.0;

				$eftDate = null;
				$eftNumber = null;

				$paymentsData = [];

				foreach ($st->getChildSegments() as $stChildSegment) {
					if ($stChildSegment instanceof FinancialInformation) {
						$eftDate = TimeFormat::formatToDB($stChildSegment->getEftDate());
						$eftNumber = $stChildSegment->getEftNumber();
					}
					if ($stChildSegment instanceof DetailSummary) {

						foreach ($stChildSegment->getChildSegments() as $segment) {
							if ($segment instanceof ClaimPaymentInformation) {

								$claimId = $segment->getReferenceIdentifier();

								$claim = $app->orm->get('Billing_Navicure_Claim', (int) $claimId);

								if ($claim->loaded()) {
									$bunchOrganizationId = $claim->case->organization_id;
									$bunchPayerId = $claim->insurance_payer_id;
									$payment = $app->orm->get('Billing_Navicure_Payment');
									$payment->claim_id = $claim->id();
									if ($segment->getTotalChargeAmount()) {
										$totalChargeAmount = (float) $segment->getTotalChargeAmount();
										$payment->total_charge_amount = $totalChargeAmount;
										$bunchTotalAmount += $totalChargeAmount;
									}
									if ($segment->getTotalPaidAmount()) {
										$totalPaidAmount = (float) $segment->getTotalPaidAmount();
										$payment->total_allowed_amount = $totalPaidAmount;
										$bunchAmountPaid += $totalPaidAmount;
									}
									if ($segment->getPatientResponsibilityAmount()) {
										$patientResponsibilityAmount = (float) $segment->getPatientResponsibilityAmount();
										$payment->patient_responsible_amount = $patientResponsibilityAmount;
										$bunchPatientResponsible += $patientResponsibilityAmount;
									}
									$payment->provider_status_code = $segment->getStatus();
									if ($segment->isPaymentProcessed()) {
										$payment->status = Payment::STATUS_READY_TO_POST;
									}
									if ($segment->isPaymentDenied()) {
										$payment->status = Payment::STATUS_HOLD;
									}

									$serviceModels = [];

									/** @var ServiceInformation $serviceInfo */
									foreach ($segment->getChildSegments() as $serviceInfo) {
										$serviceModel = $app->orm->get('Billing_Navicure_Payment_Service');
										$serviceModel->hcpcs = $serviceInfo->getServiceHcpcsCode();
										$serviceModel->quantity = $serviceInfo->getQuantity();
										$serviceModel->charge_amount = $serviceInfo->getChargeAmount();
										$serviceModel->allowed_amount = $serviceInfo->getAllowedAmount();
										$serviceModel->payment = $serviceInfo->getPaidAmount();

										$coInsAdjustmentSum = 0.00;
										$coPayAdjustmentSum = 0.00;
										$deductAdjustmentSum = 0.00;
										$otherAdjustmentSum = 0.00;

										$adjustmentModels = [];

										foreach ($serviceInfo->getAdjustments() as $adjustment) {
											if ($adjustment->getType() == \Opake\Model\Billing\Navicure\Payment\Service\Adjustment::TYPE_PATIENT_RESPONSIBILITY) {
												if ($adjustment->getReasonCode() == ServiceInformation\Adjustment::REASON_CODE_DEDUCTIBLE) { //Deductible Amount
													$deductAdjustmentSum += (float) $adjustment->getAmount();
												} else if ($adjustment->getReasonCode() == ServiceInformation\Adjustment::REASON_CODE_CO_PAY) {
													$coPayAdjustmentSum += (float) $adjustment->getAmount();
												} else if ($adjustment->getReasonCode() == ServiceInformation\Adjustment::REASON_CODE_CO_INS) {
													$coInsAdjustmentSum += (float) $adjustment->getAmount();
												}
											} else if (
												$adjustment->getType() == \Opake\Model\Billing\Navicure\Payment\Service\Adjustment::TYPE_CONTRACTUAL_OBLIGATIONS ||
												$adjustment->getType() == \Opake\Model\Billing\Navicure\Payment\Service\Adjustment::TYPE_OTHER_ADJUSTMENTS
											) {
												$otherAdjustmentSum += (float) $adjustment->getAmount();
											}

											$adjustmentModel = $app->orm->get('Billing_Navicure_Payment_Service_Adjustment');
											$adjustmentModel->type = $adjustment->getType();
											$adjustmentModel->amount = $adjustment->getAmount();
											$adjustmentModel->quantity = $adjustment->getQuantity();
											$adjustmentModel->reason_code = $adjustment->getReasonCode();

											$adjustmentModels[] = $adjustmentModel;
										}

										$serviceModel->deduct_adjustments = $deductAdjustmentSum;
										$serviceModel->co_pay_adjustments = $coPayAdjustmentSum;
										$serviceModel->co_ins_adjustments = $coInsAdjustmentSum;
										$serviceModel->other_adjustments = $otherAdjustmentSum;

										$serviceModels[] = [
											'service' => $serviceModel,
											'adjustments' => $adjustmentModels
										];
									}

									$paymentsData[] = [
										'payment' => $payment,
										'services' => $serviceModels
									];
								}
							}
						}
					}
				}

				if (!$bunchOrganizationId) {
					$app->logger->warning('835 Handling: No one submitted claim has found for a bunch');
				}

				$bunch = $app->orm->get('Billing_Navicure_Payment_Bunch');
				$bunch->organization_id = $bunchOrganizationId;
				$bunch->payer_id = $bunchPayerId;
				$bunch->eft_date = $eftDate;
				$bunch->eft_number = $eftNumber;
				$bunch->total_amount = $bunchTotalAmount;
				$bunch->amount_paid = $bunchAmountPaid;
				$bunch->patient_responsible_amount = $bunchPatientResponsible;
				$bunch->status = Payment\Bunch::STATUS_RECEIVED;

				$bunch->save();

				foreach ($paymentsData as $paymentData) {
					$paymentModel = $paymentData['payment'];
					$paymentModel->payment_bunch_id = $bunch->id();
					$paymentModel->save();
					foreach ($paymentData['services'] as $serviceData) {
						$paymentServiceModel = $serviceData['service'];
						$paymentServiceModel->payment_id = $paymentModel->id();
						$paymentServiceModel->save();
						foreach ($serviceData['adjustments'] as $adjustmentModel) {
							$adjustmentModel->payment_service_id = $paymentServiceModel->id();
							$adjustmentModel->save();
						}
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