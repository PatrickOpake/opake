<?php

namespace Opake\ActivityLogger\Action\Organization;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Comparer\OrganizationComparer;
use Opake\ActivityLogger\Extractor\Organization\OrganizationExtractor;

class OrganizationChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'organization' => $model->id()
		];
	}

	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [
			'name',
			'status',
			'address',
			'country_id',
			'website',
			'contact_name',
			'contact_email',
			'contact_phone',
			'comment',
			'federal_tax',
			'npi',
			'permissions',
		];
	}

	public function createComparer()
	{
		return new OrganizationComparer();
	}

	public function createExtractor()
	{
		return new OrganizationExtractor();
	}
}