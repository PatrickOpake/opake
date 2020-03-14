<?php

namespace OpakeAdmin\Helper\Printing\Document\Billing;

use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Ledger\PaymentInfo;
use OpakeAdmin\Helper\Printing\Document\PDFCompileDocument;

class ItemizedBillGenerator extends PDFCompileDocument
{
	/**
	 * @var \Opake\Model\Patient
	 */
	protected $patient;

	/**
	 * @var \DateTime
	 */
	protected $dateFrom;


	/**
	 * @var \DateTime
	 */
	protected $dateTo;


	/**
	 * @param \Opake\Model\Patient $patient
	 */
	public function __construct($patient)
	{
		$this->patient = $patient;
	}

	public function getFileName()
	{
		return 'itemized-bill-' . $this->patient->id() .'.pdf';
	}

	public function setDateRange($from, $to)
	{
		$this->dateFrom = $from;
		$this->dateTo = $to;
	}

	protected function generateView()
	{
		$app = \Opake\Application::get();

		$patient = $this->patient;

		if(empty($this->dateFrom) || empty($this->dateTo)) {
			throw new \Exception('Date Range is not set');
		}

		if (!$patient->loaded()) {
			throw new \Exception('Patient with id - ' . $patient->id() .' is not loaded ');
		}

		$patientRegistration = $app->orm->get('Cases_Registration');
		$patientRegistration->query
			->fields($app->db->expr('SQL_CALC_FOUND_ROWS `' . $patientRegistration->table . '`.*'));
		$patientRegistration->query->join('case', [$patientRegistration->table . '.case_id', 'case.id']);
		$patientRegistration->where('patient_id', $patient->id());
		$patientRegistration->where($app->db->expr('DATE(case.time_start)'), '>=', TimeFormat::formatToDB($this->dateFrom));
		$patientRegistration->where($app->db->expr('DATE(case.time_start)'), '<=', TimeFormat::formatToDB($this->dateTo));
		$resultPatientReg = $patientRegistration->find_all();

		$casesModel = [];
		foreach ($resultPatientReg as $key => $reg) {
			$casesModel[] = $reg->case;
		}

		$rows = [];
		$totalCharges = 0;
		$insurancePaymentsAmount = 0;
		$patientPaymentsAmount = 0;
		$adjustmentsAmount = 0;
		$writeOffsAmount = 0;
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
					$totalCharges += $item->amount;

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
						$paymentInfo = $payment->payment_info;
						$datePaid = $paymentInfo->date_of_payment;
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

						if ($paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_INSURANCE) {
							$insurancePaymentsAmount += (float) $payment->amount;
						} else if (
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_PAY ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_INSURANCE ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_DEDUCTIBLE ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_OOP
						) {
							$patientPaymentsAmount += (float) $payment->amount;
						} else if ($paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_ADJUSTMENT) {
							$adjustmentsAmount += (float) $payment->amount;
						} else if (
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_CO_PAY ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_CO_INSURANCE ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_DEDUCTIBLE ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_OOP
						) {
							$writeOffsAmount += (float) $payment->amount;
						}

						$rows[] = $row;
					}
				}
			}
		}

		$outstandingBalance = ($totalCharges - ($insurancePaymentsAmount + $patientPaymentsAmount + $adjustmentsAmount + $writeOffsAmount));
		if ($outstandingBalance < 0) {
			$outstandingBalance = 0;
		}

		$chunkedRows = array_chunk($rows, 12);

		$view = $app->view('billing/export/itemized-bill');
		$view->patient = $patient;
		$view->dateFrom = $this->dateFrom;
		$view->dateTo = $this->dateTo;
		$view->cases = $casesModel;
		$view->chunkedRows = $chunkedRows;
		$view->totalCharges = $totalCharges;
		$view->insurancePaymentsAmount = $insurancePaymentsAmount;
		$view->patientPaymentsAmount = $patientPaymentsAmount;
		$view->outstandingBalance = $outstandingBalance;

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

	protected function getPDFCompileOptions()
	{
		return [
			'margins' => '-L 0.01in -R 0.01in -T 0.4in -B 0.25in'
		];
	}
}
