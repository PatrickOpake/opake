<?php

namespace Opake\Model\Cases\Registration\Insurance;


use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

class Verification extends AbstractModel
{
	const VERIFICATION_STATUS_BEGIN = 0;
	const VERIFICATION_STATUS_CONTINUE = 1;
	const VERIFICATION_STATUS_COMPLETE = 2;

	public $id_field = 'id';
	public $table = 'case_registration_insurance_verification';
	protected $_row = [
		'id' => null,
		'case_registration_id' => null,
		'case_insurance_id' => null,

		//Out of network
		'oon_benefits' => null,
		'pre_certification_required' => 0,
		'pre_certification_obtained' => 0,
		'self_funded' => 0,
		'coverage_type' => null,
		'oon_reimbursement' => null,
		'effective_date' => '',
		'term_date' => '',
		'renewal_date' => '',
		'co_pay' => null,
		'co_insurance' => null,
		'patients_responsibility' => null,
		'individual_deductible' => null,
		'individual_met_to_date' => null,
		'individual_remaining_1' => null,
		'individual_remaining_2' => null,
		'individual_out_of_pocket_maximum' => null,
		'family_deductible' => null,
		'family_met_to_date' => null,
		'family_remaining_1' => null,
		'family_remaining_2' => null,
		'family_out_of_pocket_maximum' => null,
		'yearly_maximum' => null,
		'lifetime_maximum' => null,
		'is_oon_benefits_cap' => 0,
		'oon_benefits_cap' => null,
		'is_asc_benefits_cap' => 0,
		'asc_benefits_cap' => null,
		'is_pre_existing_clauses' => 0,
		'pre_existing_clauses' => null,
		'body_part' => null,
		'is_clauses_pertaining' => 0,
		'subscribers_name' => '',
		'authorization_number' => '',
		'expiration' => '',
		'spoke_with' => '',
		'reference_number' => '',
		'staff_member_name' => '',
		'date' => '',
		'insurance_verified' => 0,
		'is_pre_authorization_completed' => 0,
		'verification_status' => self::VERIFICATION_STATUS_BEGIN,
		'verification_completed_date' => null,
	];

	protected $belongs_to = [
		'registration' => [
			'model' => 'Cases_Registration',
			'key' => 'case_registration_id'
		],
		'insurance' => [
			'model' => 'Cases_Registration_Insurance',
			'key' => 'case_insurance_id'
		],
	];

	protected $has_many = [
		'case_types' => [
			'model' => 'Cases_Registration_Insurance_CaseType',
			'key' => 'verification_id',
			'cascade_delete' => true
		],
	];

	public function updateVerificationStatus()
	{
		$this->verification_status = static::VERIFICATION_STATUS_CONTINUE;

		if ($this->insurance_verified && $this->is_pre_authorization_completed) {
			$this->verification_status = static::VERIFICATION_STATUS_COMPLETE;
			$this->verification_completed_date = TimeFormat::formatToDBDatetime(new \DateTime());
		}
	}

	public function isVerified()
	{
		return $this->verification_status == static::VERIFICATION_STATUS_COMPLETE;
	}


	public function toArray()
	{
		$cpts = [];
		if ($this->loaded()) {
			foreach ($this->case_types->find_all() as $cpt) {
				$cpts[] = $cpt->toArray();
			}
		}

		$data = [
			'id' => $this->id(),
			'case_registration_id' => $this->case_registration_id,
			'case_insurance_id' => $this->case_insurance_id,
			'cpts' => $cpts,

			'is_oon_benefits_cap' => (int)$this->is_oon_benefits_cap,
			'oon_benefits' => (bool)$this->oon_benefits,
			'oon_benefits_cap' => $this->oon_benefits_cap,
			'is_asc_benefits_cap' => (int)$this->is_asc_benefits_cap,
			'asc_benefits_cap' => $this->asc_benefits_cap,
			'pre_existing_clauses' => $this->pre_existing_clauses,
			'body_part' => $this->body_part,
			'is_pre_existing_clauses' => (int)$this->is_pre_existing_clauses,
			'is_clauses_pertaining' => (int)$this->is_clauses_pertaining,
			'pre_certification_required' => (bool)$this->pre_certification_required,
			'pre_certification_obtained' => (bool)$this->pre_certification_obtained,
			'self_funded' => (bool)$this->self_funded,
			'coverage_type' => $this->coverage_type,
			'oon_reimbursement' => $this->oon_reimbursement,
			'effective_date' => $this->effective_date,
			'term_date' => $this->term_date,
			'renewal_date' => $this->renewal_date,
			'co_pay' => $this->co_pay,
			'co_insurance' => $this->co_insurance,
			'patients_responsibility' => $this->patients_responsibility,
			'individual_deductible' => $this->individual_deductible,
			'individual_met_to_date' => $this->individual_met_to_date,
			'individual_remaining_1' => $this->individual_remaining_1,
			'individual_remaining_2' => $this->individual_remaining_2,
			'individual_out_of_pocket_maximum' => $this->individual_out_of_pocket_maximum,
			'family_deductible' => $this->family_deductible,
			'family_met_to_date' => $this->family_met_to_date,
			'family_remaining_1' => $this->family_remaining_1,
			'family_remaining_2' => $this->family_remaining_2,
			'family_out_of_pocket_maximum' => $this->family_out_of_pocket_maximum,
			'yearly_maximum' => $this->yearly_maximum,
			'lifetime_maximum' => $this->lifetime_maximum,
			'subscribers_name' => $this->subscribers_name,
			'authorization_number' => $this->authorization_number,
			'expiration' => $this->expiration,
			'spoke_with' => $this->spoke_with,
			'reference_number' => $this->reference_number,
			'staff_member_name' => $this->staff_member_name,
			'date' => $this->date,
			'insurance_verified' => (bool) $this->insurance_verified,
			'is_pre_authorization_completed' => (bool) $this->is_pre_authorization_completed,
			'verification_status' => (int) $this->verification_status,
			'verification_completed_date' => $this->verification_completed_date,
		];

		return $data;
	}
}