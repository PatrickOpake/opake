<?php

namespace Opake\Formatter\Billing\Navicure\Payment\Bunch;

use Opake\Formatter\Billing\Navicure\Payment\BasePaymentFormatter;
use Opake\Model\Billing\Navicure\Payment\Bunch;

class ListEntryFormatter extends BasePaymentFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
			    'payer_name',
			    'eft_date',
			    'eft_number',
			    'number_of_claims',
			    'total_amount',
			    'amount_paid',
			    'patient_responsible_amount',
			    'balance',
			    'status_text',
			    'status'
			],
			'fieldMethods' => [
				'id' => 'int',
			    'payer_name' => 'deferred',
			    'eft_date' => 'toDate',
			    'number_of_claims' => 'numberOfClaims',
			    'total_amount' => 'money',
			    'amount_paid' => 'money',
			    'patient_responsible_amount' => 'money',
			    'balance' => 'balance',
			    'status_text' => 'statusText',
			    'status' => 'int'
			]
		]);
	}

	protected function prepareDeferredData($data, $fields)
	{
		$hasPayerName = in_array('payer_name', $fields);
		if ($hasPayerName) {
			$data['payer_name'] = null;
			$firstPayment = $this->model->payments->find();
			if ($firstPayment->loaded()) {
				$claim = $firstPayment->claim;
				if ($firstPayment->claim->loaded()) {
					if ($claim->primary_insurance->loaded()) {

						$ins = \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance::wrapCaseInsurance($claim->primary_insurance);
						$ins->setUsePayerDataIfMissed(true);
						$data['payer_name'] = $ins->getInsuranceCompanyName();

					} else {
						$insurancePayerId = $claim->insurance_payer_id;
						if ($insurancePayerId) {
							$payerModel = $this->pixie->orm->get('Insurance_Payor', $insurancePayerId);
							if ($payerModel->loaded()) {
								$data['payer_name'] = $payerModel->name;
							}
						}
					}
				}
			}
		}

		return $data;
	}

	protected function formatNumberOfClaims($name, $options, $model)
	{
		return $model->payments->count_all();
	}

	protected function formatBalance($name, $options, $model)
	{
		$count = $model->total_amount;
		if ($model->amount_paid) {
			$count -= $model->amount_paid;
		}
		if ($model->patient_responsible_amount) {
			$count -= $model->patient_responsible_amount;
		}

		return $this->_formatFloatToMoney($count);
	}

	protected function formatStatusText($name, $options, $model)
	{
		$statusId = $model->status;
		$statusesList = Bunch::getStatusesList();

		return (isset($statusesList[$statusId])) ? $statusesList[$statusId] : '';
	}
}