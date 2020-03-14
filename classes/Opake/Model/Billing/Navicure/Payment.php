<?php

namespace Opake\Model\Billing\Navicure;

use Opake\Model\AbstractModel;

class Payment extends AbstractModel
{
	const STATUS_READY_TO_POST = 1;
	const STATUS_HOLD = 2;
	const STATUS_EXCEPTION = 3;
	const STATUS_PROCESSED = 4;
	const STATUS_RESUBMITTED = 5;

	public $id_field = 'id';
	public $table = 'billing_navicure_payment';
	protected $_row = [
		'id' => null,
	    'claim_id' => null,
	    'payment_bunch_id' => null,
	    'total_charge_amount' => null,
	    'total_allowed_amount' => null,
	    'patient_responsible_amount' => null,
	    'provider_status_code' => null,
	    'status' => null
	];

	protected $belongs_to = [
		'claim' => [
			'model' => 'Billing_Navicure_Claim',
			'key' => 'claim_id',
		],
	];

	protected $has_many = [
		'services' => [
			'model' => 'Billing_Navicure_Payment_Service',
		    'key' => 'payment_id'
		]
	];

	protected $formatters = [
		'ListEntry' => [
			'class' => '\Opake\Formatter\Billing\Navicure\Payment\ListEntryFormatter'
		]
	];

	public static function getProviderStatusDescriptionList()
	{
		return [
			'1' => 'Processed as Primary',
		    '2' => 'Processed as Secondary',
		    '3' => 'Processed as Tertiary',
		    '4' => 'Denied',
		    '19' => 'Processed as Primary, Forwarded to Additional Payer(s)',
		    '20' => 'Processed as Secondary, Forwarded to Additional Payer(s)',
		    '21' => 'Processed as Tertiary, Forwarded to Additional Payer(s)',
		    '22' => 'Reversal of Previous Payment',
		    '23' => 'Not Our Claim, Forwarded to Additional Payer(s)',
		    '25' => 'Predetermination Pricing Only - No Payment',
		];
	}
}