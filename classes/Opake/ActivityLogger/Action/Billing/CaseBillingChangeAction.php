<?php

namespace Opake\ActivityLogger\Action\Billing;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Comparer\CaseBillingComparer;
use Opake\ActivityLogger\Extractor\CaseBilling\CaseBillingExtractor;

class CaseBillingChangeAction extends ModelAction
{

	protected function getSearchParams()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'case_id' => $model->case_id
		];
	}

	protected function getFieldsForCompare()
	{
		return [
			'rendering_provider_id',
			'rendering_provider_npi',
			'bill_provider_id',
			'bill_provider_npi',
			'pre_auth',
			'place_of_service_id',
			'facility_name',
			'prior_payments',
			'admission_type',
			'discharge_status_id',
			'apcs',
			'drgs',
			'final_diagnosis',
			'admit_diagnosis',
			'procedures',
			'occurences',
			'supplies',
			'notes',
		];
	}

	/**
	 * @return CaseBillingExtractor
	 */
	protected function createExtractor()
	{
		return new CaseBillingExtractor();
	}

	/**
	 * @return CaseBillingComparer
	 */
	protected function createComparer()
	{
		return new CaseBillingComparer();
	}
}