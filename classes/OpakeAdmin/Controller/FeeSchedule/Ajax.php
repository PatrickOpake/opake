<?php

namespace OpakeAdmin\Controller\FeeSchedule;

use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionList()
	{
		$this->checkAccess('sites', 'view');
		$siteId = $this->request->param('subid');
		$model = $this->pixie->orm->get('Billing_FeeSchedule_Record');

		$search = new \OpakeAdmin\Model\Search\FeeSchedule($this->pixie);
		$search->setOrganizationId($this->org->id());
		$search->setSiteId($siteId);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $item) {
			$items[] = $item->toArray();
		}
		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionSiteInfo()
	{
		$this->checkAccess('sites', 'view');
		$siteId = $this->request->param('subid');
		$site = $this->orm->get('Site', $siteId);

		if (!$site->loaded()) {
			throw new PageNotFound();
		}

		$this->result = [
			'hasFeeSchedule' => $site->hasFeeSchedule(),
		    'siteName' => $site->name
		];
	}

	public function actionSitesList()
	{
		$this->checkAccess('sites', 'view');
		$sites = $this->orm->get('Site');
		$sites = $sites->where('organization_id', $this->org->id())
			->where('active', 1)
			->find_all();

		$results = [];

		foreach ($sites as $site) {
			$results[] = $site->getFormatter('List')->toArray();
		}

		$this->result = $results;
	}

	public function actionUploadFeeSchedule()
	{
		$this->checkAccess('sites', 'view');
		$siteId = $this->request->param('subid');
		$typeFeeSchedule = $this->request->post('type');
		$files = $this->request->getFiles();
		if (empty($files['file'])) {
			throw new BadRequest('File is required');
		}

		$file = $files['file'];
		if ($file->isEmpty() || $file->hasErrors()) {
			throw new \Exception('Error while file uploading');
		}

		$type = $file->getType();

		$allowedTypes = \OpakeAdmin\Helper\Import\FeeSchedule::getAllowedMimeTypes();

		if (!in_array($type, $allowedTypes)) {
			$this->result = [
				'success' => false,
				'errors' => ['Uploaded file is not supported for import']
			];
			return;
		}

		$tmpFile = new \Opake\Helper\File\TemporaryFile($file);
		$tmpFile->create();

		try {
			$importer = new \OpakeAdmin\Helper\Import\FeeSchedule($this->pixie);
			$importer->setOrganizationId($this->org->id());
			$importer->setSiteId($siteId);
			$importer->setType($typeFeeSchedule);
			$importer->load($tmpFile->getFilePath());
			$tmpFile->cleanup();
		} catch (\Exception $e) {
			$tmpFile->cleanup();
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
			return;
		}

		$this->result = [
			'success' => true
		];
	}
}
