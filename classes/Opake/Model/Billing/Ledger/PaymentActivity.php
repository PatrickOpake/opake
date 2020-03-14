<?php

namespace Opake\Model\Billing\Ledger;

use Opake\Model\AbstractModel;

class PaymentActivity extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'billing_ledger_payment_activity';
	protected $_row = [
		'id' => null,
		'applied_payment_id' => null,
		'activity_date' => null,
		'activity_user_id' => null,
		'patient_id' => null,
		'date_of_payment' => null,
		'payment_source' => null,
		'payment_method' => null,
		'selected_patient_insurance_id' => null,
		'payment_amount' => null
	];

	protected $belongs_to = [
		'patient' => [
			'model' => 'Patient',
			'key' => 'patient_id'
		],
	];

	protected $formatters = [
		'ListEntry' => [
			'class' => '\Opake\Formatter\Billing\Ledger\PaymentActivity\ListEntryFormatter'
		]
	];
}