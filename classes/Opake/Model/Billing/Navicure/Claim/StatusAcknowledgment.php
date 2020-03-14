<?php

namespace Opake\Model\Billing\Navicure\Claim;

use Opake\Model\AbstractModel;

class StatusAcknowledgment extends AbstractModel
{
	const STATUS_ACCEPTED = 1;
	const STATUS_REJECTED = 2;

	public $id_field = 'id';
	public $table = 'billing_navicure_claim_status_acknowledgment';
	protected $_row = [
		'id' => null,
	    'claim_id' => null,
	    'date' => null,
	    'amount' => null,
	    'status' => null,
	    'note' => null
	];

	protected $formatters = [
		'Coding' => [
			'class' => 'Opake\Formatter\Billing\Navicure\Claim\Coding\StatusAcknowledgmentFormatter'
		]
	];


}