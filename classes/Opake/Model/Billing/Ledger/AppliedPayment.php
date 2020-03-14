<?php

namespace Opake\Model\Billing\Ledger;

use Opake\Model\AbstractModel;

class AppliedPayment extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'billing_ledger_applied_payment';
	protected $_row = [
		'id' => null,
		'coding_bill_id' => null,
	    'payment_info_id' => null,
	    'amount' => null,
	    'resp_co_pay_amount' => null,
	    'resp_co_ins_amount' => null,
	    'resp_deduct_amount' => null,
	    'claim_id' => null,
		'related_parent_payment_id' => null
	];

	protected $belongs_to = [
		'coding_bill' => [
			'model' => 'Cases_Coding_Bill',
		    'key' => 'coding_bill_id'
		],
		'payment_info' => [
			'model' => 'Billing_Ledger_PaymentInfo',
			'key' => 'payment_info_id'
		]
	];

	protected $formatters = [
		'ListEntry' => [
			'class' => '\Opake\Formatter\Billing\Ledger\AppliedPayment\ListEntryFormatter'
		],
		'PaymentActivityListEntry' => [
			'class' => '\Opake\Formatter\Billing\Ledger\AppliedPayment\PaymentActivityListEntryFormatter'
		]
	];
}