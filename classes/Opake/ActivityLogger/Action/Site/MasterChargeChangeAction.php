<?php

namespace Opake\ActivityLogger\Action\Site;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Extractor\Site\SiteExtractor;

class MasterChargeChangeAction extends ModelAction
{


	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		$site = $this->pixie->orm->get('Site', $model->site_id);

		$details = [];
		$details['charge_id'] = $model->id();
		if ($site && $site->loaded()) {
			$details['site_name'] = $site->name;
		}
		return $details;
	}

	protected function getFieldsForCompare()
	{
		return [
			'cdm',
			'desc',
			'amount',
			'revenue_code',
			'department',
			'cpt',
			'cpt_modifier1',
			'cpt_modifier2',
			'unit_price',
			'ndc',
			'active',
			'general_ledger',
			'notes',
			'historical_price',
		];
	}
}