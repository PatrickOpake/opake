<?php

namespace Opake\Formatter\Billing\FeeSchedule;

use Opake\Formatter\BaseDataFormatter;

class BaseFormatter extends BaseDataFormatter
{

	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'organization_id',
			    'site_id',
			    'hcpcs',
			    'mod',
			    'procedure_indicator',
			    'amount',
			    'fc_mod_amount',
			    'fb_mod_amount',
			    'penalty_price',
			    'fc_mod_penalty_price',
			    'fb_mod_penalty_price'
			],
			'fieldMethods' => [
				'id' => 'int',
				'organization_id' => 'int',
			    'site_id' => 'int',
			]
		]);
	}


}
