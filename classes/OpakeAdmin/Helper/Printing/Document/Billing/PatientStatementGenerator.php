<?php

namespace OpakeAdmin\Helper\Printing\Document\Billing;

use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Ledger\PaymentActivity;
use Opake\Model\Billing\Ledger\PaymentInfo;
use OpakeAdmin\Helper\Printing\Document\PDFCompileDocument;

class PatientStatementGenerator extends PDFCompileDocument
{
	/**
	 * @var \Opake\Model\Patient
	 */
	protected $patient;

	/**
	 * @var string
	 */
	protected $comment;


	/**
	 * @param \Opake\Model\Patient $patient
	 */
	public function __construct($patient)
	{
		$this->patient = $patient;
	}

	/**
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * @param string $comment
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
	}

	public function getFileName()
	{
		return 'patient-statement-' . $this->patient->id() .'.pdf';
	}

	protected function generateView()
	{
		$app = \Opake\Application::get();

		$patient = $this->patient;

		if (!$patient->loaded()) {
			throw new \Exception('Patient with id - ' . $patient->id() .' is not loaded ');
		}

		$patientRegistration = $app->orm->get('Cases_Registration')
			->where('patient_id', $patient->id())
			->find_all();

		$casesModel = [];
		foreach ($patientRegistration as $reg) {
			$casesModel[] = $reg->case;
		}

		$rows = [];
		$sumOfAllCases = 0;
		$patientResponsibilityBalances = [];
		$patientResponsibilityBalancesTotal = 0;
		foreach ($casesModel as $case)  {
			if ($case->coding->loaded()) {

				foreach ($case->coding->bills->find_all() as $item) {
					$row = [];

					$appliedPayments = $item->applied_payments
						->find_all()
						->as_array();

					$balance = (float) $item->amount;
					foreach ($appliedPayments as $payment) {
						$balance -= $payment->amount;
					}

					$lastInsuranceAppliedDate = null;
					foreach ($appliedPayments as $payment) {
						if ($payment->payment_info->payment_source == PaymentInfo::PAYMENT_SOURCE_INSURANCE) {
							$paymentDate = TimeFormat::fromDBDate($payment->payment_info->date_of_payment);
							if ($paymentDate && ($lastInsuranceAppliedDate === null || $lastInsuranceAppliedDate->getTimestamp() < $paymentDate->getTimestamp())) {
								$lastInsuranceAppliedDate = $paymentDate;
							}
						}
					}
					if ($lastInsuranceAppliedDate === null) {
						$lastInsuranceAppliedDate = new \DateTime();
					}

					$patientResponsibilityCalculator = new \OpakeAdmin\Helper\Billing\Ledger\PatientResponsibilityCalculator($patient, $case, $item);
					$patientResponsibilityCalculator->setAppliedPayments($appliedPayments);
					$respDetails = $patientResponsibilityCalculator->calculateResponsibilityDetails();

					$patientRespBalance = ($respDetails['coPay'] + $respDetails['coIns'] + $respDetails['deductible'] + $respDetails['oop']);
					$patientResponsibilityBalances[] = [$lastInsuranceAppliedDate, $patientRespBalance];
					$patientResponsibilityBalancesTotal += $patientRespBalance;

					$chargeMasterRecord = $item->getChargeMasterEntry();
					$row['date'] = $case->time_start;
					$row['procedure'] = $chargeMasterRecord ? $chargeMasterRecord->cpt : '';
					$row['desc'] = $chargeMasterRecord ? $chargeMasterRecord->desc : '';
					$row['charge'] = $item->charge;
					$row['amount'] = $item->amount;
					$row['applied_payments'] = $appliedPayments;
					$row['type'] = 'case';
					$row['responsibility'] = $respDetails;
					$rows[] = $row;

					foreach ($appliedPayments as $payment) {
						$datePaid = $payment->payment_info->date_of_payment;
						$datePaid = new \DateTime($datePaid);
						$datePaid = TimeFormat::getDate($datePaid);
						$amount = $payment->amount;
						$row = [];
						$row['date'] = $datePaid;
						$row['desc'] = $this->getDesc($payment);
						$row['credit'] = $amount;
						$row['type'] = 'payment';
						$row['responsibility'] = [];

						if ($payment->resp_co_pay_amount) {
							$row['responsibility']['coPay'] = (float) $payment->resp_co_pay_amount;
						}
						if ($payment->resp_co_ins_amount) {
							$row['responsibility']['coIns'] = (float) $payment->resp_co_ins_amount;
						}
						if ($payment->resp_deduct_amount) {
							$row['responsibility']['deductible'] = (float) $payment->resp_deduct_amount;
						}

						$rows[] = $row;
					}

					$sumOfAllCases += $balance;
				}
			}
		}

		$chunkedRows = array_chunk($rows, 12);

		$view = $app->view('billing/export/patient-statement');
		$view->patient = $patient;
		$view->cases = $casesModel;
		$view->chunkedRows = $chunkedRows;
		$view->comment = $this->comment;
		$view->sumOfAllCases = $sumOfAllCases;
		$view->patientResponsibilityBalances = [
			'30' => $this->getBalanceByDays($patientResponsibilityBalances, 0, 30),
			'60' => $this->getBalanceByDays($patientResponsibilityBalances, 31, 60),
			'90' => $this->getBalanceByDays($patientResponsibilityBalances, 61, 90),
		    '120' => $this->getBalanceByDays($patientResponsibilityBalances, 91, 120),
		    '120p' => $this->getBalanceByDays($patientResponsibilityBalances, 120)
		];
		$view->patientResponsibilityBalancesTotal = $patientResponsibilityBalancesTotal;

		return $view;
	}

	protected function getDesc($payment)
	{
		$app = \Opake\Application::get();
		$source = $payment->payment_info->payment_source;
		if ($source == PaymentInfo::PAYMENT_SOURCE_INSURANCE) {
			$insurance = $app->orm->get('Patient_Insurance', $payment->payment_info->selected_patient_insurance_id);
			if ($insurance->loaded()) {
				return $insurance->getTitle();
			}
		}

		$titlesList = PaymentInfo::getPaymentSourcesList();
		return (isset($titlesList[$source])) ? $titlesList[$source] : '';
	}

	protected function getBalanceByDays($balances, $lower = null, $upper = null)
	{
		$sum = 0;
		$currentDate = new \DateTime();
		foreach ($balances as $balance) {
			$date = $balance[0];
			$amount = $balance[1];
			$interval = $date->diff($currentDate);
			$days = (int)$interval->format('%R%a');
			if ($upper === null) {
				if ($days > $lower) {
					$sum += $amount;
				}
			} else if ($lower === null) {
				if ($days < $lower) {
					$sum += $amount;
				}
			} else {
				if ($days >= $lower && $days <= $upper) {
					$sum += $amount;
				}
			}
		}
		return $sum;
	}

	public static function getCommentOptions()
	{
		return [
			'Prior authorization was needed at the time of visit and you did provide us with one. Your insurance denied payment.',
			'A referral was needed at the time of visit and you did not provide us with one. Your insurance denied payment.',
			'According to Americhoice/Medicaid - No insurance for Date of Service',
			'Application Denied No Coverage - Patient responsibility',
			'Applied toward patient deductible',
			'Co-Pay',
			'Coverage not in effect at the time of service',
			'HSA deductible',
			'Insurance issued check to member and member cashed check',
			'Medicare denied payment stating other insurance, however, we have no other insurance information on file',
			'No Bill',
			'No Insurance on File - Bill is your responsibility',
			'Non covered',
			'Non covered by Medicaid',
			'Our attempts to collect money paid to you have gone unanswered. We will send a 1099 to the IRS',
			'Please remit payment immediately or we will be forced to send your account to collections',
			'Per your insurance, the co-pay due is greater than what you paid at time of visit',
			'Services denied by your insurance - No out of network benefits',
			'Thank you for your most recent payment. Your account has been updated',
			'The amount due is your additional co-pay/coinsurance responsibility',
			'This amount is your coinsurance due upon receipt of this bill',
			'This amount is your facility copay. Payment is due upon receipt of this bill',
			'This amount is your office visit copay. Payment is due upon receipt of this bill',
			'We do not accept assignment, please pay member',
			'We do not participate with Charity Care, but you qualified for a charity discount. This is your new bill',
			'We do not participate with Medical Assistance. Please remit payment today',
			'We need your insurance information',
			'Your insurance company has increased your copay. Contact your carrier if you have any questions',
			'Your insurance company has processed charges directly to you. Please remit payment',
			'Your plan needs more info from you. Contact them or this bill is your responsibility',
			'Your secondary insurance does not coordinate benefits with your primary insurance. Please remit payment.',
		];
	}

	protected function getPDFCompileOptions()
	{
		return [
			'margins' => '-L 0.01in -R 0.01in -T 0.4in -B 0.25in'
		];
	}
}
