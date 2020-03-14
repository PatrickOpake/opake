<?php

namespace Opake\Formatter\Billing\Ledger\AppliedPayment;

use Opake\Formatter\BaseDataFormatter;
use Opake\Formatter\Billing\Ledger\PaymentActivity\FormFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Ledger\PaymentActivity;
use Opake\Model\Billing\Ledger\PaymentInfo;

class ListEntryFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'amount',
				'date',
				'payment_source',
				'payment_method',
			    'selected_patient_insurance_id',
			    'is_insurance_payment',
			    'is_patient_payment',
			    'is_adjustment',
			    'is_write_off',
			    'resp_co_pay_amount',
			    'resp_co_ins_amount',
			    'resp_deduct_amount',
			    'authorization_number',
			    'check_number'
			],
			'fieldMethods' => [
				'id' => 'int',
				'date' => 'paymentDate',
				'amount' => ['float', [
					'round' => 2,
				    'nullAsZero' => true
				]],
				'selected_patient_insurance_id' => 'selectedPatientInsuranceId',
				'payment_source' => 'paymentSource',
				'payment_method' => 'paymentMethod',
			    'is_insurance_payment' => 'isInsurancePayment',
			    'is_patient_payment' => 'isPatientPayment',
			    'is_adjustment' => 'isAdjustment',
			    'is_write_off' => 'isWriteOff',
				'resp_co_pay_amount' => ['float', [
					'round' => 2
				]],
				'resp_co_ins_amount' => ['float', [
					'round' => 2
				]],
				'resp_deduct_amount' => ['float', [
					'round' => 2
				]],
			    'authorization_number' => 'authorizationNumber',
			    'check_number' => 'checkNumber'
			]
		]);
	}

	protected function formatPaymentDate($name, $options, $model)
	{
		$paymentInfo = $model->payment_info;
		$date = $paymentInfo->date_of_payment;
		$date = TimeFormat::fromDBDate($date);
		if ($date) {
			return TimeFormat::getDate($date);
		}

		return null;
	}

	protected function formatPaymentSource($name, $options, $model)
	{
		$paymentInfo = $model->payment_info;
		return [
			'id' => $paymentInfo->payment_source,
		    'title' => $this->_getPaymentSourceName($model),
		    'patient_insurance_id' => $paymentInfo->selected_patient_insurance_id
		];
	}

	protected function formatPaymentMethod($name, $options, $model)
	{
		$paymentInfo = $model->payment_info;
		$method = $paymentInfo->payment_method;

		$titlesList = PaymentInfo::getPaymentMethodsList();
		$title = (isset($titlesList[$method])) ? $titlesList[$method] : '';

		return [
			'id' => $method,
		    'title' => $title
		];
	}

	protected function formatIsInsurancePayment($name, $options, $model)
	{
		$paymentInfo = $model->payment_info;
		return ($paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_INSURANCE);

	}

	protected function formatIsPatientPayment($name, $options, $model)
	{
		$paymentInfo = $model->payment_info;
		return (
			$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_PAY ||
			$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_INSURANCE ||
			$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_DEDUCTIBLE ||
			$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_OOP
		);
	}

	protected function formatIsAdjustment($name, $options, $model)
	{
		$paymentInfo = $model->payment_info;
		return ($paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_ADJUSTMENT);
	}

	protected function formatIsWriteOff($name, $options, $model)
	{
		$paymentInfo = $model->payment_info;
		return ($paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF);
	}

	protected function formatSelectedPatientInsuranceId($name, $options, $model)
	{
		$paymentInfo = $model->payment_info;
		return $paymentInfo->selected_patient_insurance_id;
	}

	protected function formatCheckNumber($name, $options, $model)
	{
		$paymentInfo = $model->payment_info;
		return $paymentInfo->check_number;
	}

	protected function formatAuthorizationNumber($name, $options, $model)
	{
		$paymentInfo = $model->payment_info;
		return $paymentInfo->authorization_number;
	}

	protected function _getPaymentSourceName($model)
	{
		$paymentInfo = $model->payment_info;
		$source = $paymentInfo->payment_source;
		if ($source == PaymentInfo::PAYMENT_SOURCE_INSURANCE) {
			$insurance = $this->pixie->orm->get('Patient_Insurance', $paymentInfo->selected_patient_insurance_id);
			if ($insurance->loaded()) {
				return $insurance->getTitle();
			}
		}

		$titlesList = PaymentInfo::getPaymentSourcesList();
		return (isset($titlesList[$source])) ? $titlesList[$source] : '';
	}

}