<?php

namespace OpakeAdmin\Form\Cases\Coding;

use Opake\Form\AbstractForm;
use Opake\Model\Cases\Coding;

class CodingForm extends AbstractForm
{

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('lab_services_outside_amount')->rule('decimal')->rule('between', 0, 99999999.99)->error('Incorrect lab services outside amount value');
		$validator->field('amount_paid_by_other_insurance')->rule('decimal')->rule('between', 0, 99999999.99)->error('Incorrect lab services outside amount value');
		$validator->field('amount_paid')->rule('decimal')->rule('between', 0, 99999999.99)->error('Incorrect amount paid value');
	}

	protected function prepareValues($data)
	{
		$billType = $data['bill_type'] ?? null;
		if ($billType != Coding::BILL_TYPE_REPLACEMENT_OF_PRIOR_CLAIM) {
			$data['original_claim_id'] = null;
		}
		if ($billType != Coding::BILL_TYPE_CANCEL_OF_PRIOR_CLAIM) {
			$data['reference_number'] = null;
		}

		return parent::prepareValues($data);
	}

	protected function getFields()
	{
		return array_merge(parent::getFields(), [
			'authorization_release_information_payment',
			'discharge_code',
			'condition_codes',
			'bill_type',
			'reference_number',
			'has_lab_services_outside',
			'lab_services_outside_amount',
			'insurance_order',
			'amount_paid_by_other_insurance',
			'amount_paid',
			'addition_claim_information',
			'remarks',
			'original_claim_id',
		]);
	}

}
