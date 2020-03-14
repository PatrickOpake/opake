<?php

namespace Opake\Model\Billing\Ledger;

use Opake\Model\AbstractModel;

class ApplyingOptions extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'billing_ledger_applying_options';
	protected $_row = [
		'id' => null,
		'coding_bill_id' => null,
		'is_force_patient_resp' => null
	];
}