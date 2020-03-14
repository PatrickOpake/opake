<?php

namespace Opake\Model\Billing\Navicure\Payment;

use Opake\Model\AbstractModel;

class Service extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'billing_navicure_payment_service';
	protected $_row = [
		'id' => null,
	    'payment_id' => null,
	    'hcpcs' => null,
	    'quantity' => null,
	    'charge_amount' => null,
	    'allowed_amount' => null,
	    'payment' => null,
	    'deduct_adjustments' => null,
	    'co_pay_adjustments' => null,
	    'co_ins_adjustments' => null,
        'other_adjustments' => null,
	    'provider_status_code' => null
	];

	protected $has_many = [
		'adjustments' => [
			'model' => 'Billing_Navicure_Payment_Service_Adjustment',
			'key' => 'payment_service_id',
			'cascade_delete' => true
		]
	];

	protected $formatters = [
		'ListEntry' => [
			'class' => '\Opake\Formatter\Billing\Navicure\Payment\Service\ListEntryFormatter'
		]
	];

	public function getCoInsCoPayAdjustmentsSum()
	{
		$coPayAmount = ($this->co_pay_adjustments !== null) ? (float) $this->co_pay_adjustments  : 0.00;
		$coInsAmount = ($this->co_ins_adjustments !== null) ? (float) $this->co_ins_adjustments  : 0.00;

		return ($coPayAmount + $coInsAmount);
	}
}