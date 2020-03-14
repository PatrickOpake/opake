<?php

namespace Opake\Formatter\Billing\Ledger;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;

class BillFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'code',
				'amount',
				'dx1',
			    'dx2',
			    'dx3',
			    'dx4',
			    'payments',
				'has_force_patient_resp'
			],
			'fieldMethods' => [
				'id' => 'int',
				'code' => 'serviceCode',
				'amount' => ['float', [
					'round' => 2,
					'nullAsZero' => true
				]],
				'description' => 'shortDesc',
			    'dx1' => 'deferred',
			    'dx2' => 'deferred',
			    'dx3' => 'deferred',
			    'dx4' => 'deferred',
			    'payments' => 'payments',
				'has_force_patient_resp' => 'hasForcePatientResponsibility'
			]
		]);
	}

	/**
	 * @param $data
	 * @param $fields
	 * @return mixed
	 */
	protected function prepareDeferredData($data, $fields)
	{
		$diagnoses = $this->model->getDiagnoses();

		$data['dx1'] = null;
		$data['dx2'] = null;
		$data['dx3'] = null;
		$data['dx4'] = null;

		if (isset($diagnoses[0])) {
			$data['dx1'] = $diagnoses[0]->icd->code;
		}
		if (isset($diagnoses[1])) {
			$data['dx2'] = $diagnoses[1]->icd->code;
		}
		if (isset($diagnoses[2])) {
			$data['dx3'] = $diagnoses[2]->icd->code;
		}
		if (isset($diagnoses[3])) {
			$data['dx4'] = $diagnoses[3]->icd->code;
		}

		return $data;
	}

	protected function formatServiceCode($name, $options, $model)
	{
		$chargeMasterRecord = $model->getChargeMasterEntry();
		if ($chargeMasterRecord) {
			return $chargeMasterRecord->cpt;
		}

		return '';
	}

	protected function formatPayments($name, $options, $model)
	{
		$payments = [];
		$activityEntries = $model->applied_payments->find_all();
		foreach ($activityEntries as $entry) {
			$payments[] = $entry->getFormatter('ListEntry')
				->toArray();
		}

		return $payments;
	}

	protected function formatHasForcePatientResponsibility($name, $options, $model)
	{
		$app = \Opake\Application::get();

		$applyingOptions = $app->orm->get('Billing_Ledger_ApplyingOptions')
			->where('coding_bill_id', $model->id())
			->find();

		if (!$applyingOptions->loaded()) {
			return null;
		}

		return ((bool) $applyingOptions->is_force_patient_resp);
	}
}