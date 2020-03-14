<?php

namespace Opake\Model\Billing\Navicure\Claim;

use Opake\Model\Insurance\AbstractType;

class Insurance extends AbstractType
{
	public $id_field = 'id';
	public $table = 'billing_navicure_claim_insurance_types';
	protected $_row = [
		'id' => null,
		'type' => null,
		'order' => null,
		'case_registration_insurance_id' => null,
		'insurance_data_id' => null
	];

	protected $belongs_to = [
		'case_insurance' => [
			'model' => 'Cases_Registration_Insurance',
			'key' => 'case_registration_insurance_id'
		]
	];
}