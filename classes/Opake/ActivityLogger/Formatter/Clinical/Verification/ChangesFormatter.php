<?php

namespace Opake\ActivityLogger\Formatter\Clinical\Verification;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;
use Opake\Model\Cases\Registration;
use Opake\Model\Patient;
use OpakeAdmin\Helper\Analytics\Reports\CasesReport\Formatter;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'insurance_verified':
			case 'is_pre_authorization_completed':
			case 'oon_benefits':
			case 'pre_certification_required':
			case 'pre_certification_obtained':
			case 'self_funded':
			case 'is_oon_benefits_cap':
			case 'is_asc_benefits_cap':
			case 'is_pre_existing_clauses':
			case 'body_part':
			case 'is_clauses_pertaining':
				return FormatterHelper::formatYesNo($value);
			case 'coverage_type':
				return FormatterHelper::formatKeyValueSource($value, Patient::getCoverageTypes());

			case 'oon_reimbursement' :
				return FormatterHelper::formatKeyValueSource($value, Registration::getReimbursment());





		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'insurance_verified' => 'Insurance Verified?',
			'is_pre_authorization_completed' => 'Pre-Authorization Completed?',
			'oon_benefits' => 'Out of Network Benefits?',
			'pre_certification_required' => 'Pre-Certification Required?',
			'pre_certification_obtained'=> 'Pre-Certification Obtained?',
			'self_funded'=> 'Self Funded?',
			'coverage_type'=> 'Coverage Type',
			'oon_reimbursement'=> 'Out-Of-Network Reimbursement',
			'effective_date'=> 'Effective Date',
			'term_date'=> 'Term Date',
			'renewal_date'=> 'Renewal Date',
			'co_pay'=> 'Co-Pay ($)',
			'co_insurance'=> 'Co-Insurance (%)',
			'patients_responsibility'=> 'Patients Responsibility (%)',
			'individual_deductible'=> 'Individual Deductible ($)',
			'individual_met_to_date'=> 'Met-to-Date ($)',
			'individual_remaining_1'=> 'Remaining ($)',
			'individual_out_of_pocket_maximum'=> 'Out of Pocket Maximum ($)',
			'individual_remaining_2'=> 'Remaining ($)',
			'family_deductible'=> 'Family Deductible ($)',
			'family_met_to_date'=> 'Met-to-Date ($)',
			'family_remaining_1'=> 'Remaining ($)',
			'family_out_of_pocket_maximum'=> 'Out of Pocket Maximum ($)',
			'family_remaining_2'=> 'Remaining ($)',
			'yearly_maximum'=> 'Yearly Maximum ($)',
			'lifetime_maximum'=> 'Lifetime Maximum ($)',
			'is_oon_benefits_cap'=> 'IS THERE AN OON BENEFITS CAP ON THE PATIENT’S POLICY?',
			'is_asc_benefits_cap'=> 'IS THERE AN ASC BENEFITS CAP ON THE PATIENT’S POLICY?',
			'is_pre_existing_clauses'=> 'ARE THERE ANY PRE-EXISTING CLAUSES UNDER PATIENT’S POLICY?',
			'body_part'=> 'IF YES, WHAT BODY PART?',
			'is_clauses_pertaining'=> 'ARE THERE ANY CLAUSES PERTAINING TO MEDICARE ENTITLEMENT?',
			'subscribers_name'=> 'Subscribers Name',
			'authorization_number'=> 'Authorization #',
			'expiration'=> 'Expiration',
			'spoke_with'=> 'Spoke With',
			'reference_number'=> 'Reference #',
			'staff_member_name'=> 'Staff Member Name',
			'date'=> 'Date',
		];
	}
}