<?php

namespace Opake\Model\Billing\Ledger;

use Opake\Model\AbstractModel;

class InterestPayment extends AbstractModel
{
	public $id_field = 'id';

	public $table = 'billing_ledger_interest_payments';

	protected $_row = [
		'id' => null,
		'case_id' => null,
		'amount' => null,
		'date' => null
	];

	protected $belongs_to = [
		'case' => [
			'model' => 'Cases\Item',
			'key' => 'case_id'
		],
	];

	protected $formatters = [
		'ListEntry' => [
			'class' => '\Opake\Formatter\Billing\Ledger\InterestPayment\ListEntryFormatter'
		]
	];
}