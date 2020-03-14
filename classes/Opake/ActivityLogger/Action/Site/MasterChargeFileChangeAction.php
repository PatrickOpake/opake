<?php

namespace Opake\ActivityLogger\Action\Site;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Extractor\Site\SiteExtractor;

class MasterChargeFileChangeAction extends ModelAction
{


	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		$siteId = $this->getExtractor()->getAdditionalInfo('site_id');
		$site = $this->pixie->orm->get('Site', $siteId);

		$details = [];
		$details['uploaded_file_id'] = $model->id();
		if ($site && $site->loaded()) {
			$details['site_name'] = $site->name;
		}
		return $details;
	}
}