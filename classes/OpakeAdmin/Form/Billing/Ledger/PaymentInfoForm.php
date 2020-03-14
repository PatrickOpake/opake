<?php

namespace OpakeAdmin\Form\Billing\Ledger;

use Opake\Form\AbstractForm;

class PaymentInfoForm extends AbstractForm
{
	protected function getFields()
	{
		return [
			'selected_patient_insurance_id',
			'date_of_payment',
			'payment_source',
			'payment_method',
			'total_amount',
		    'authorization_number',
		    'check_number'
		];
	}
}