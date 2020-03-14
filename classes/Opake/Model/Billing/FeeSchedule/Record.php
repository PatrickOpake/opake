<?php

namespace Opake\Model\Billing\FeeSchedule;

use Opake\Model\AbstractModel;

class Record extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'billing_fee_schedule';
	protected $_row = [
		'id' => null,
	    'organization_id' => null,
	    'site_id' => null,
	    'type' => null,
	    'hcpcs' => null,
	    'description' => null,
	    'contracted_rate' => null,
	];

	public function toArray()
	{
		$data = parent::toArray();
		$data['contracted_rate'] = '$' . number_format((float)$this->contracted_rate, 2, '.', ',');
		return $data;
	}
}