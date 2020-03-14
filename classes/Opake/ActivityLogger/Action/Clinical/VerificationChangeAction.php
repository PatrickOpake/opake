<?php

namespace Opake\ActivityLogger\Action\Clinical;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Extractor\Clinical\CaseVerificationExtractor;

class VerificationChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();

		return [
			'registration_id' => $model->case_registration_id,
			'case_id' => $model->registration->case->id,
			'patient' => $model->registration->getFullName(),
			'insurance' => $model->insurance->getTitle(),
		];
	}

	/**
	 * @return array
	 */
	protected function getSearchParams()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'case_id' => $model->registration->case->id,
			'patient_id' => $model->registration->patient_id,
		];
	}

	protected function getFieldsForCompare()
	{
		return [
			'oon_benefits',
			'pre_certification_required',
			'pre_certification_obtained',
			'self_funded',
			'coverage_type',
			'oon_reimbursement',
			'effective_date',
			'term_date',
			'renewal_date',
			'co_pay',
			'co_insurance',
			'patients_responsibility',
			'individual_deductible',
			'individual_met_to_date',
			'individual_remaining_1',
			'individual_remaining_2',
			'individual_out_of_pocket_maximum',
			'family_deductible',
			'family_met_to_date',
			'family_remaining_1',
			'family_remaining_2',
			'family_out_of_pocket_maximum',
			'yearly_maximum',
			'lifetime_maximum',
			'is_oon_benefits_cap',
			'oon_benefits_cap',
			'is_asc_benefits_cap',
			'asc_benefits_cap',
			'is_pre_existing_clauses',
			'pre_existing_clauses',
			'body_part',
			'is_clauses_pertaining',
			'subscribers_name',
			'authorization_number',
			'expiration',
			'spoke_with',
			'reference_number',
			'staff_member_name',
			'date',
			'insurance_verified',
			'is_pre_authorization_completed',
			'verification_status',
			'verification_completed_date',
		];
	}

	protected function createExtractor()
	{
		return new CaseVerificationExtractor();
	}

}