<?php

namespace OpakeAdmin\Controller\Billings\Ledger;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;
use Opake\Exception\PageNotFound;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Billing\Ledger\PaymentInfo;
use Opake\Model\Patient\Insurance;
use OpakeAdmin\Form\Billing\Ledger\PaymentActivityForm;
use OpakeAdmin\Form\Billing\Ledger\PaymentInfoForm;
use OpakeAdmin\Helper\Printing\Document\Billing\PatientStatementGenerator;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionPatientCases()
	{
		$this->checkAccess('billing', 'view');

		$patientId = $this->request->param('subid');

		if (!$patientId) {
			throw new BadRequest('Patient ID is required');
		}

		$patient = $this->pixie->orm->get('Patient', $patientId);
		if ($patient->organization_id != $this->org->id()) {
			throw new Forbidden();
		}

		$model = $this->pixie->orm->get('Cases_Item');
		$query = $model->query;
		$query->fields('case.*');
		$query->join('case_registration', ['case_registration.case_id', 'case.id']);
		$query->where('case_registration.patient_id', $patientId);

		$query->order_by('case.time_start', 'DESC');
		$query->order_by('id', 'DESC');

		$patientCases = [];
		foreach ($model->find_all() as $case) {
			$patientCases[] = $case->getFormatter('LedgerListEntry')
				->toArray();
		}

		$this->result = [
			'success' => true,
			'cases' => $patientCases
		];
	}

	public function actionPatients()
	{
		$this->checkAccess('billing', 'view');

		$search = new \OpakeAdmin\Model\Search\Billing\Patient($this->pixie);
		$search->setOrganizationId($this->org->id());
		$models = $search->search(
			$this->pixie->orm->get('Patient'),
			$this->request
		);

		$result = [];
		foreach ($models as $model) {
			$result[] = $model->getFormatter('BillingLedgerListEntry')->toArray();
		}

		$this->result = [
			'items' => $result,
		    'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionLedger()
	{
		$this->checkAccess('billing', 'view');

		$patientId = $this->request->param('subid');

		if (!$patientId) {
			throw new BadRequest('Patient ID is required');
		}

		$patient = $this->pixie->orm->get('Patient', $patientId);
		if ($patient->organization_id != $this->org->id()) {
			throw new Forbidden();
		}

		$totalChargesAmount = 0;
		$insurancePaymentsAmount = 0;
		$patientPaymentsAmount = 0;
		$adjustmentsAmount = 0;
		$writeOffsAmount = 0;
		$patientResponsibleBalance = 0;

		$patientRegistration = $this->pixie->orm->get('Cases_Registration')
			->where('patient_id', $patientId)
			->find_all()->as_array();

		foreach ($patientRegistration as $registration) {
			$coding = $registration->case->coding;
			if ($coding->loaded()) {
				foreach ($coding->bills->find_all() as $bill) {
					$totalChargesAmount += (float) $bill->amount;

					$appliedPayments = $bill->applied_payments->find_all()->as_array();


					$patientResponsibilityCalculator = new \OpakeAdmin\Helper\Billing\Ledger\PatientResponsibilityCalculator($patient, $registration->case, $bill);
					$patientResponsibilityCalculator->setAppliedPayments($appliedPayments);

					$patientResponsibleBalance += $patientResponsibilityCalculator->calculateResponsibilityBalance();

					foreach ($appliedPayments as $appliedPayment) {
						$paymentInfo = $appliedPayment->payment_info;
						if ($paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_INSURANCE) {
							$insurancePaymentsAmount += (float) $appliedPayment->amount;
						} else if (
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_PAY ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_INSURANCE ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_DEDUCTIBLE ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_OOP
						) {
							$patientPaymentsAmount += (float) $appliedPayment->amount;
						} else if ($paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_ADJUSTMENT) {
							$adjustmentsAmount += (float) $appliedPayment->amount;
						} else if (
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_CO_PAY ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_CO_INSURANCE ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_DEDUCTIBLE ||
							$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_OOP
						) {
							$writeOffsAmount += (float) $appliedPayment->amount;
						}
					}
				}
			}
		}

		foreach ($patientRegistration as $registration) {
			$case = $registration->case;
			foreach ($case->ledger_interest_payments->find_all() as $interestPayment) {
				$amount = (float) $interestPayment->amount;
				$totalChargesAmount += $amount;
				$insurancePaymentsAmount += $amount;
			}
		}

		$outstandingBalance = ($totalChargesAmount - ($insurancePaymentsAmount + $patientPaymentsAmount + $adjustmentsAmount + $writeOffsAmount));
		if ($outstandingBalance < 0) {
			$outstandingBalance = 0;
		}

		$paymentSources = [];
		$patientHasInsurances = false;
		$insuranceModels = $patient->insurances
			->where('type', 'NOT IN',
				$this->pixie->db->expr('(' . implode(', ', [
					Insurance::INSURANCE_TYPE_SELF_PAY,
					Insurance::INSURANCE_TYPE_LOP
				]) .')'))
			->find_all();
		foreach ($insuranceModels as $insuranceModel) {
			$patientHasInsurances = true;
			$paymentSources[] = [
				'patient_insurance_id' => $insuranceModel->id(),
				'title' => $insuranceModel->getTitle(),
				'id' => PaymentInfo::PAYMENT_SOURCE_INSURANCE
			];
		}

		$paymentSourcesList = PaymentInfo::getPaymentSourcesList();
		foreach ($paymentSourcesList as $typeId => $label) {
			if ($typeId != PaymentInfo::PAYMENT_SOURCE_INSURANCE) {
				$paymentSources[] = [
					'title' => $label,
					'id' => $typeId
				];
			}
		}

		$patientStatementCommentOptions = [];
		$patientStatementCommentOptions[] = [
			'id' => null,
		    'title' => ''
		];

		foreach (PatientStatementGenerator::getCommentOptions() as $index => $option) {
			$patientStatementCommentOptions[] = [
				'id' => $index,
			    'title' => $option
			];
		}

		$this->result = [
			'success' => true,
		    'ledger' => [
			    'patient' => $patient->getFormatter('BillingLedger')->toArray(),
		        'totals' => [
			        'total_charges' => $totalChargesAmount,
		            'insurance_payments' => $insurancePaymentsAmount,
		            'patient_payments' => $patientPaymentsAmount,
		            'adjustments' => $adjustmentsAmount,
		            'write_offs' => $writeOffsAmount,
		            'outstanding_balance' => $outstandingBalance,
		            'patient_responsible_balance' => $patientResponsibleBalance
		        ],
		        'payment_sources' => $paymentSources,
		        'statement_comment_options' => $patientStatementCommentOptions,
		        'patient_has_insurances' => $patientHasInsurances
		    ]
		];
	}

	public function actionStatementHistory()
	{
		$this->checkAccess('billing', 'view');

		$search = new \OpakeAdmin\Model\Search\Billing\PatientStatement\History($this->pixie);
		$search->setOrganizationId($this->org->id);
		$models = $search->search(
			$this->pixie->orm->get('Billing_PatientStatement_History'),
			$this->request
		);

		$result = [];
		foreach ($models as $model) {
			$result[] = $model->toArray();
		}

		$this->result = [
			'items' => $result,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionApplyPayments()
	{
		$this->checkAccess('billing', 'view');
		try {
			$this->pixie->db->begin_transaction();

			$data = $this->getData(true);
			$paymentInfo = $data['payment_info'];
			$appliedPayments = $data['applied_payments'];

			if ($appliedPayments) {
				$paymentInfoModel = $this->orm->get('Billing_Ledger_PaymentInfo');
				$paymentInfoForm = new PaymentInfoForm($this->pixie, $paymentInfoModel);
				$paymentInfoForm->load($paymentInfo);
				$paymentInfoForm->save();

				$lastNotAdditionalPayment = null;

				foreach ($appliedPayments as $paymentData) {

					$isAdditionalPayment = (!empty($paymentData['is_additional_payment']));

					$payment = $this->orm->get('Billing_Ledger_AppliedPayment');
					$payment->coding_bill_id = $paymentData['coding_bill_id'];
					$payment->amount = $paymentData['amount'];

					if (!empty($paymentData['custom_payment_info'])) {
						$currentPaymentInfo = $this->orm->get('Billing_Ledger_PaymentInfo');
						$currentPaymentInfoForm = new PaymentInfoForm($this->pixie, $currentPaymentInfo);
						$currentPaymentInfoForm->load($paymentData['custom_payment_info']);
						$currentPaymentInfoForm->save();
						$payment->payment_info_id = $currentPaymentInfo->id();
					} else {
						$payment->payment_info_id = $paymentInfoModel->id();
					}

					if ($isAdditionalPayment && $lastNotAdditionalPayment) {
						$payment->related_parent_payment_id = $lastNotAdditionalPayment->id();
					}

					if (!empty($paymentData['resp_co_pay_amount'])) {
						$payment->resp_co_pay_amount = $paymentData['resp_co_pay_amount'];
					}
					if (!empty($paymentData['resp_co_ins_amount'])) {
						$payment->resp_co_ins_amount = $paymentData['resp_co_ins_amount'];
					}
					if (!empty($paymentData['resp_deduct_amount'])) {
						$payment->resp_deduct_amount = $paymentData['resp_deduct_amount'];
					}

					$payment->save();

					$this->pixie->activityLogger
						->newModelActionQueue($payment)
						->addAction(ActivityRecord::ACTION_BILLING_LEDGER_PAYMENTS_APPLIED)
						->assign()
						->registerActions();

					if (!$isAdditionalPayment) {
						$lastNotAdditionalPayment = $payment;
					}

					$paymentModels[] = $payment;
				}
			}

			$this->pixie->db->commit();
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->pixie->db->rollback();
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
			return;
		}

		$this->result = [
			'success' => true
		];
	}

	public function actionUpdatePayment()
	{
		$this->checkAccess('billing', 'view');
		$data = $this->getData();

		$paymentId = $data->payment_id;

		$payment = $this->orm->get('Billing_Ledger_AppliedPayment', $paymentId);
		if (!$payment->loaded()) {
			throw new PageNotFound();
		}

		$payment->amount = $data->amount;

		if (isset($data->resp_co_pay_amount)) {
			$payment->resp_co_pay_amount = $data->resp_co_pay_amount;
		}
		if (isset($data->resp_co_ins_amount)) {
			$payment->resp_co_ins_amount = $data->resp_co_ins_amount;
		}
		if (isset($data->resp_deduct_amount)) {
			$payment->resp_deduct_amount = $data->resp_deduct_amount;
		}

		if (!empty($data->payment_source) || !empty($data->payment_method)) {
			$paymentInfo = $payment->payment_info;
			if ($paymentInfo->payments->count_all() > 1) {

				$newPaymentInfo = $this->pixie->orm->get('Billing_Ledger_PaymentInfo');
				$newPaymentInfo->date_of_payment = $paymentInfo->date_of_payment;
				$newPaymentInfo->total_amount = $payment->amount;
				$newPaymentInfo->selected_patient_insurance_id = $paymentInfo->selected_patient_insurance_id;
				$newPaymentInfo->payment_source = $paymentInfo->payment_source;
				$newPaymentInfo->payment_method = $paymentInfo->payment_method;
				$newPaymentInfo->authorization_number = $paymentInfo->authorization_number;
				$newPaymentInfo->check_number = $paymentInfo->check_number;

				$newPaymentInfo->save();
				$payment->payment_info_id = $newPaymentInfo->id();
				$paymentInfo = $newPaymentInfo;
			}

			if (!empty($data->payment_source)) {
				$paymentInfo->payment_source = $data->payment_source->id;
				if (isset($data->payment_source->patient_insurance_id)) {
					$paymentInfo->selected_patient_insurance_id = $data->payment_source->patient_insurance_id;
				} else {
					$paymentInfo->selected_patient_insurance_id = null;
				}
			}

			if (!empty($data->payment_method)) {
				$paymentInfo->payment_method = $data->payment_method->id;
			}

			if (isset($data->authorization_number)) {
				$paymentInfo->authorization_number = $data->authorization_number;
			}

			if (isset($data->check_number)) {
				$paymentInfo->check_number = $data->check_number;
			}

			$paymentInfo->save();
		}

		$this->pixie->activityLogger
			->newModelActionQueue($payment)
			->addAction(ActivityRecord::ACTION_BILLING_LEDGER_PAYMENTS_EDITED)
			->assign()
			->registerActions();

		$payment->save();

		$this->pixie->events->fireEvent('Billing.Ledger.PaymentUpdated', $payment);

		$this->result = [
			'success' => true
		];
	}


	public function actionDeletePayment()
	{
		$this->checkAccess('billing', 'view');
		$data = $this->getData();

		$paymentId = $data->payment_id;

		$payment = $this->orm->get('Billing_Ledger_AppliedPayment', $paymentId);
		if (!$payment->loaded()) {
			throw new PageNotFound();
		}

		$this->pixie->db->begin_transaction();

		try {
			$this->pixie->activityLogger
				->newModelActionQueue($payment)
				->addAction(ActivityRecord::ACTION_BILLING_LEDGER_PAYMENTS_DELETED)
				->assign()
				->registerActions();

			/** @var PaymentInfo $paymentInfo */
			$paymentInfo = $payment->payment_info;
			if ($paymentInfo->payments->count_all() > 1) {
				$paymentInfo->total_amount = $paymentInfo->total_amount - $payment->amount;
				$paymentInfo->save();
			}
			else {
				$paymentInfo->delete();
			}

			$payment->delete();
			$this->pixie->db->commit();
		}
		catch (\Exception $e) {
			$this->logSystemError($e);
			$this->pixie->db->rollback();
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
			return;
		}

		$this->result = [
			'success' => true,
		];
	}

	public function actionForceAssignToInsurance()
	{
		$this->_applyingOptionsForceAssign(0);
	}

	public function actionForceAssignToPatient()
	{
		$this->_applyingOptionsForceAssign(1);
	}

	protected function _applyingOptionsForceAssign($valueToAssign)
	{
		$patientId = $this->request->param('subid');

		$data = $this->getData();
		$procedureIds = $data->procedures;

		if (!$procedureIds) {
			throw new BadRequest('Procedures are required');
		}

		$patient = $this->pixie->orm->get('Patient', $patientId);
		if (!$patient->loaded()) {
			throw new PageNotFound('Patient is not found');
		}

		if ($patient->organization_id != $this->org->id()) {
			throw new Forbidden();
		}

		foreach ($procedureIds as $procedureId) {
			$bill = $this->pixie->orm->get('Cases_Coding_Bill', $procedureId);
			if ($bill->coding->case->organization_id != $this->org->id()) {
				throw new Forbidden();
			}
			$applyingOptions = $this->pixie->orm->get('Billing_Ledger_ApplyingOptions')
				->where('coding_bill_id', $bill->id())
				->find();

			if (!$applyingOptions->loaded()) {
				$applyingOptions = $this->pixie->orm->get('Billing_Ledger_ApplyingOptions');
				$applyingOptions->coding_bill_id = $bill->id();
			}

			$applyingOptions->is_force_patient_resp = $valueToAssign;
			$applyingOptions->save();
		}

		$newPatientResponsibleBalance = 0;

		$patientRegistration = $this->pixie->orm->get('Cases_Registration')
			->where('patient_id', $patientId)
			->find_all();

		foreach ($patientRegistration as $registration) {
			$coding = $registration->case->coding;
			if ($coding->loaded()) {
				foreach ($coding->bills->find_all() as $bill) {
					$patientResponsibilityCalculator = new \OpakeAdmin\Helper\Billing\Ledger\PatientResponsibilityCalculator($patient, $registration->case, $bill);
					$newPatientResponsibleBalance += $patientResponsibilityCalculator->calculateResponsibilityBalance();
				}
			}
		}


		$this->result = [
			'success' => true,
		    'new_patient_responsible_balance' => $newPatientResponsibleBalance
		];
	}

}