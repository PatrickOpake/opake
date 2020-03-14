<?php

namespace Opake\Formatter\Billing\Navicure\Payment;

use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Payment;

class ListEntryFormatter extends BasePaymentFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
			    'claim_id',
			    'patient_first_name',
			    'patient_last_name',
			    'mrn',
			    'insurance_company_name',
			    'dos',
			    'total_charge_amount',
			    'total_allowed_amount',
			    'total_deduct',
			    'total_co_pay_co_ins',
			    'provider_status_code',
			    'status',
			    'services'
			],
			'fieldMethods' => [
				'id' => 'int',
			    'claim_id' => 'int',
			    'patient_first_name' => 'patientFirstName',
			    'patient_last_name' => 'patientLastName',
			    'mrn' => 'mrn',
			    'insurance_company_name' => 'insuranceCompany',
			    'dos' => 'dos',
			    'total_charge_amount' => 'money',
			    'total_allowed_amount' => 'money',
			    'total_deduct' => 'deferred',
			    'total_co_pay_co_ins' => 'deferred',
				'status' => 'int',
				'provider_status_code' => 'providerStatusCodeDescription',
			    'services' => ['relationshipMany', [
				    'formatter' => [
					        'class' => 'Opake\Formatter\Billing\Navicure\Payment\Service\ListEntryFormatter',
				        ]
			        ]
			    ]
			]
		]);
	}

	protected function prepareDeferredData($data, $fields)
	{
		$hasDeduct = in_array('total_deduct', $fields);
		$hasCoPayCoIns = in_array('total_co_pay_co_ins', $fields);
		if ($hasCoPayCoIns || $hasDeduct) {
			$services = $this->model->services->find_all();
			$deductTotal = 0.00;
			$coInsCoPayTotal = 0.00;
			foreach ($services as $service) {
				if ($service->deduct_adjustments) {
					$deductTotal += (float) $service->deduct_adjustments;
				}
				$coInsCoPayTotal += $service->getCoInsCoPayAdjustmentsSum();
			}

			$data['total_deduct'] = $this->_formatFloatToMoney($deductTotal);
			$data['total_co_pay_co_ins'] = $this->_formatFloatToMoney($coInsCoPayTotal);
		}

		return $data;
	}

	protected function formatPatientFirstName($name, $options, $model)
	{
		return $model->claim->first_name;
	}

	protected function formatPatientLastName($name, $options, $model)
	{
		return $model->claim->last_name;
	}

	protected function formatMrn($name, $options, $model)
	{
		return $model->claim->mrn;
	}

	protected function formatInsuranceCompany($name, $options, $model)
	{

		if ($model->claim->primary_insurance->loaded()) {
			return $model->claim->primary_insurance->getInsuranceName();
		}

		$insurancePayerId = $model->claim->insurance_payer_id;
		if ($insurancePayerId) {
			$payerModel = $this->pixie->orm->get('Insurance_Payor', $insurancePayerId);
			if ($payerModel->loaded()) {
				return $payerModel->name;
			}
		}

		return '';

	}

	protected function formatDos($name, $optons, $model)
	{
		$value = $model->claim->dos;
		$date = TimeFormat::fromDBDatetime($value);
		if ($date) {
			return TimeFormat::getDate($date);
		}

		return null;
	}

	protected function formatProviderStatusCodeDescription($name, $options, $model)
	{
		$list = Payment::getProviderStatusDescriptionList();
		$name = $model->provider_status_code;
		$name .= ((isset($list[$model->provider_status_code])) ? (' - ' . $list[$model->provider_status_code] . '') : '');

		return $name;
	}
}