<?php

namespace Opake\Model\Billing\Ledger;

use Opake\Model\AbstractModel;

class PaymentInfo extends AbstractModel
{

	const PAYMENT_SOURCE_INSURANCE = 1;
	const PAYMENT_SOURCE_PATIENT_CO_PAY = 2;
	const PAYMENT_SOURCE_PATIENT_DEDUCTIBLE = 3;
	const PAYMENT_SOURCE_PATIENT_CO_INSURANCE = 4;
	const PAYMENT_SOURCE_PATIENT_OOP = 5;
	const PAYMENT_SOURCE_ADJUSTMENT = 6;
	const PAYMENT_SOURCE_WRITE_OFF = 7;
	const PAYMENT_SOURCE_WRITE_OFF_CO_PAY = 8;
	const PAYMENT_SOURCE_WRITE_OFF_CO_INSURANCE = 9;
	const PAYMENT_SOURCE_WRITE_OFF_DEDUCTIBLE = 10;
	const PAYMENT_SOURCE_WRITE_OFF_OOP = 11;

	const PAYMENT_METHOD_CASH = 1;
	const PAYMENT_METHOD_CHECK = 2;
	const PAYMENT_METHOD_CREDIT_CARD = 3;
	const PAYMENT_METHOD_DEBIT_CARD = 4;
	const PAYMENT_METHOD_ELECTRONIC = 5;

	public $id_field = 'id';
	public $table = 'billing_ledger_payment_info';
	protected $_row = [
		'id' => null,
		'date_of_payment' => null,
		'selected_patient_insurance_id' => null,
		'payment_source' => null,
		'payment_method' => null,
		'total_amount' => null,
		'authorization_number' => null,
		'check_number' => null
	];

	protected $has_many = [
		'payments' => [
			'model' => 'Billing_Ledger_AppliedPayment',
			'key' => 'payment_info_id',
			'cascade_delete' => false
		],
	];


	public static function getPaymentSourcesList()
	{
		return [
			static::PAYMENT_SOURCE_INSURANCE => 'Patient Insurance',
		    static::PAYMENT_SOURCE_PATIENT_CO_PAY => 'Patient Co-Pay',
		    static::PAYMENT_SOURCE_PATIENT_DEDUCTIBLE => 'Patient Deductible',
		    static::PAYMENT_SOURCE_PATIENT_CO_INSURANCE => 'Patient Co-Insurance',
		    static::PAYMENT_SOURCE_PATIENT_OOP => 'Patient OOP',
		    static::PAYMENT_SOURCE_ADJUSTMENT => 'Adjustment',
		    static::PAYMENT_SOURCE_WRITE_OFF => 'Write-Off',
		    static::PAYMENT_SOURCE_WRITE_OFF_CO_PAY => 'Co-Pay Write-Off',
		    static::PAYMENT_SOURCE_WRITE_OFF_CO_INSURANCE => 'Co-Insurance Write-Off',
		    static::PAYMENT_SOURCE_WRITE_OFF_DEDUCTIBLE => 'Deductible Write-Off',
		    static::PAYMENT_SOURCE_WRITE_OFF_OOP => 'OOP Write-Off'
		];
	}

	public static function getPaymentMethodsList()
	{
		return [
			static::PAYMENT_METHOD_CASH => 'Cash',
		    static::PAYMENT_METHOD_CHECK => 'Check',
		    static::PAYMENT_METHOD_CREDIT_CARD => 'Credit Card',
		    static::PAYMENT_METHOD_DEBIT_CARD => 'Debit Card',
		    static::PAYMENT_METHOD_ELECTRONIC => 'Electronic'
		];
	}

}