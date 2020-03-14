<?php

namespace Opake\Formatter\Billing\Ledger\AppliedPayment;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Ledger\PaymentInfo;

class PaymentActivityListEntryFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'patient_first_name',
				'patient_last_name',
				'date_of_payment',
				'payment_source',
				'payment_method',
				'amount'
			],
			'fieldMethods' => [
				'id' => 'int',
				'patient_first_name' => 'patientFirstName',
				'patient_last_name' => 'patientLastName',
				'date_of_payment' => 'dateOfPayment',
				'payment_source' => 'paymentSource',
				'payment_method' => 'paymentMethod',
			    'amount' =>  ['float', [
				    'round' => 2,
			        'nullAsZero' => true
			    ]]
			]
		]);
	}

	protected function formatDateOfPayment($name, $options, $model)
	{
		$date = TimeFormat::fromDBDate($model->payment_info->date_of_payment);

		if ($date) {
			return TimeFormat::getDate($date);
		}

		return null;
	}

	protected function formatPatientFirstName($name, $options, $model)
	{
		$patient = $model->coding_bill->coding->case->registration->patient;
		return $patient->first_name;
	}

	protected function formatPatientLastName($name, $options, $model)
	{
		$patient = $model->coding_bill->coding->case->registration->patient;
		return $patient->last_name;
	}

	protected function formatPaymentSource($name, $options, $model)
	{
		$source = $model->payment_info->payment_source;
		if ($source == PaymentInfo::PAYMENT_SOURCE_INSURANCE) {
			$insurance = $this->pixie->orm->get('Patient_Insurance', $model->payment_info->selected_patient_insurance_id);
			if ($insurance->loaded()) {
				return $insurance->getTitle();
			}
		}

		$titlesList = PaymentInfo::getPaymentSourcesList();
		return (isset($titlesList[$source])) ? $titlesList[$source] : '';
	}

	protected function formatPaymentMethod($name, $options, $model)
	{
		$method = $model->payment_info->payment_method;

		$titlesList = PaymentInfo::getPaymentMethodsList();
		$title = (isset($titlesList[$method])) ? $titlesList[$method] : '';

		return $title;
	}

}