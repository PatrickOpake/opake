<?php

namespace Opake\Model\Billing\FeeSchedule;

use Opake\Model\AbstractModel;

class Info extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'billing_fee_schedule_info';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'site_id' => null,
		'cbsa' => null,
	    'effective_date' => null
	];
}