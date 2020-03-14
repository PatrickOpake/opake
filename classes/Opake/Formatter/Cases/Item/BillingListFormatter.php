<?php

namespace Opake\Formatter\Cases\Item;

use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;

class BillingListFormatter extends ItemFormatter
{
	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'patient',
				'dos',
				'procedure_name_for_dashboard',
				'verification_status',
				'coding_id',
				'has_claim',
				'notes_count',
				'financial_doc_count',
				'has_billing_flagged_comments',
				'submitted_claims',
				'insurances',
				'is_ready_professional_claim',
				'is_ready_institutional_claim'
			],

			'fieldMethods' => [
				'patient' => ['patient', [
						'formatter' => [
							'class' => '\Opake\Formatter\BaseDataFormatter',
							'fields' => [
								'first_name',
								'last_name',
							]
						]
					]
				],
				'dos' => 'dos',
				'procedure_name_for_dashboard' =>  ['modelMethod', [
					'modelMethod' => 'getProcedureNameForDashboard'
					]
				],
				'verification_status' => 'verificationStatus',
				'coding_id' => 'codingId',
				'has_claim' => 'hasClaim',
				'notes_count' => 'notesCount',
				'financial_doc_count' => 'financialDocCount',
				'has_billing_flagged_comments' => ['modelMethod', [
					'modelMethod' => 'hasFlaggedBillingComments'
					]
				],
				'submitted_claims' => 'submittedClaims',
				'insurances' => 'insurances',
				'is_ready_institutional_claim' => 'isReadyInstitutionalClaim',
				'is_ready_professional_claim' => 'isReadyProfessionalClaim'
			]
		]);
	}

	protected function formatDos($name, $options, $model)
	{
		return $model->time_start;
	}

	protected function formatVerificationStatus($name, $options, $model)
	{
		return $model->registration->verification_status;
	}

	protected function formatCodingId($name, $options, $model)
	{
		return $model->coding->id();
	}

	protected function formatIsReadyInstitutionalClaim($name, $options, $model)
	{
		$isReady = (bool) $model->coding->is_ready_institutional_claim;
		return $isReady;
	}

	protected function formatIsReadyProfessionalClaim($name, $options, $model)
	{
		$isReady = (bool) $model->coding->is_ready_professional_claim;
		return $isReady;
	}

	protected function formatHasClaim($name, $options, $model)
	{
		$count = $this->pixie->orm->get('Billing_Navicure_Claim')
			->where('case_id', $model->id())
			->count_all();

		$hasClaim = false;
		if ($count > 0) {
			$hasClaim = true;
		}

		return $hasClaim;
	}

	protected function formatNotesCount($name, $options, $model)
	{
		return (int) $model->getBillingNotesCount();
	}

	protected function formatFinancialDocCount($name, $options, $model)
	{
		return (int) $model->getFinancialDocuments()->count_all();
	}

	protected function formatSubmittedClaims($name, $options, $model)
	{
		$claims = [];

		$queryClaims = $this->pixie->orm->get('Billing_Navicure_Claim')
			->where('case_id', $model->id())
			->limit(4)
			->find_all();

		foreach ($queryClaims as $item) {
			$types = Claim::getListOfClaimType();
			if (isset($types[$item->type])) {
				$dt = TimeFormat::fromDBDatetime($item->last_transaction_date);
				$claims[] = $types[$item->type] . ' - ' . (string)TimeFormat::getDate($dt);
			}
		}

		return $claims;
	}

	protected function formatInsurances($name, $options, $model)
	{
		$result = [];
		foreach ($model->registration->getSelectedInsurances() as $insurance) {
			$result[] = $insurance->getInsuranceName();
		}

		return implode(', ', $result);
	}

}