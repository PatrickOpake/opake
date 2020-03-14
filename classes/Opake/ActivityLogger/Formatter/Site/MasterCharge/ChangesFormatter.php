<?php

namespace Opake\ActivityLogger\Formatter\Site\MasterCharge;

use Opake\ActivityLogger\DefaultFormatter;

class ChangesFormatter extends DefaultFormatter
{

	protected function getLabels()
	{
		return [
			'charge_id' => 'Charge ID',
			'cdm' => 'Charge Code',
			'desc' => 'Description',
			'amount' => 'Charge Amount',
			'revenue_code' => 'Revenue Code',
			'department' => 'Department No.',
			'cpt' => 'CPT/HCPCS',
			'cpt_modifier1' => 'CPT Modifier 1',
			'cpt_modifier2' => 'CPT Modifier 2',
			'unit_price' => 'Unit Cost',
			'ndc' => 'NDC',
			'active' => 'Active ',
			'general_ledger' => 'GL-Code',
			'notes' => 'Notes',
			'historical_price' => 'Historical Price',
		];
	}
}

