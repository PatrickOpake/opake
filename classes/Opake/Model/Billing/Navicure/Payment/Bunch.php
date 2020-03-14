<?php

namespace Opake\Model\Billing\Navicure\Payment;

use Opake\Model\AbstractModel;

class Bunch extends AbstractModel
{
	const STATUS_RECEIVED = 1;
	const STATUS_IN_PROGRESS = 2;
	const STATUS_PROCESSED = 3;

	public $id_field = 'id';
	public $table = 'billing_navicure_payment_bunch';
	protected $_row = [
		'id' => null,
		'payer_id' => null,
		'eft_date' => null,
		'eft_number' => null,
	    'total_amount' => null,
	    'amount_paid' => null,
	    'patient_responsible_amount' => null,
	    'status' => self::STATUS_RECEIVED
	];

	protected $belongs_to = [
		'payer' => [
			'model' => 'Insurance_Payor',
			'key' => 'payer_id'
		],
	];

	protected $has_many = [
		'payments' => [
			'model' => 'Billing_Navicure_Payment',
			'key' => 'payment_bunch_id',
			'cascade_delete' => false
		],
	];

	protected $formatters = [
		'ListEntry' => [
			'class' => '\Opake\Formatter\Billing\Navicure\Payment\Bunch\ListEntryFormatter'
		]
	];

	public static function getStatusesList()
	{
		return [
			self::STATUS_RECEIVED => 'Received',
		    self::STATUS_IN_PROGRESS => 'In Progress',
		    self::STATUS_PROCESSED => 'Processed'
		];
	}
}