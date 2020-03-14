<?php

namespace Opake\ActivityLogger\Action\Site;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Extractor\Site\SiteExtractor;

class SiteChangeAction extends ModelAction
{


	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'site' => $model->id()
		];
	}

	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [
			'name',
			'departments',
			'locations',
			'description',
			'comment',
			'country',
			'state',
			'city',
			'zip_code',
			'website',
			'contact_name',
			'contact_phone',
			'contact_email',
			'contact_fax',
			'pay_country',
			'pay_state',
			'pay_city',
			'pay_zip_code',
		    'navicure_sftp_username',
		    'navicure_sftp_password',
			'navicure_submitter_id',
			'navicure_submitter_password'
		];
	}

	/**
	 * @return SiteExtractor
	 */
	protected function createExtractor()
	{
		return new SiteExtractor();
	}
}