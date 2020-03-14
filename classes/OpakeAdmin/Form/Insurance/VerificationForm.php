<?php

namespace OpakeAdmin\Form\Insurance;


use Opake\Form\AbstractForm;
use Opake\Helper\Arrays;
use Opake\Helper\TimeFormat;
use Opake\Model\Geo\City;

class VerificationForm extends AbstractForm
{
	/**
	 * @var array
	 */
	protected $cpts = [];

	/**
	 * @return array
	 */
	public function getCpts()
	{
		return $this->cpts;
	}

	/**
	 * @return array
	 */
	protected function getFields()
	{
		return [
			'case_registration_id',
			'case_insurance_id',
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
			'cpts'
		];
	}

	/**
	 * @param array $data
	 * @return array
	 * @throws \Exception
	 */
	protected function prepareValues($data)
	{
		$result = parent::prepareValues($data);

		$this->cpts = [];
		if (isset($result['cpts'])) {
			foreach ($result['cpts'] as $cpt) {
				$this->cpts[] = $this->cptToArray($cpt);
			};
			unset($result['cpts']);
		}

		return $result;
	}

	/**
	 * @param \Opake\Extentions\Validate\Validator $validator
	 */
	protected function setValidationRules($validator)
	{
	}


	private function cptToArray($cpt)
	{
		$result = Arrays::copyProperties([], (array)$cpt, ['id', 'verification_id', 'is_pre_authorization', 'pre_authorization']);
		$result['case_type_id'] = !empty($cpt->case_type) ? $cpt->case_type->id : null;

		return $result;
	}
}