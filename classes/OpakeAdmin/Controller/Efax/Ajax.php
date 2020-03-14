<?php

namespace OpakeAdmin\Controller\Efax;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;
use OpakeAdmin\Helper\Printing\Document\Common\UploadedFileDocument;
use OpakeAdmin\Helper\Printing\PrintCompiler;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	protected $user;
	protected $userSites;

	public function before()
	{
		parent::before();
		$this->iniUser();
	}

	public function actionList()
	{
		$items = [];

		$search = new \OpakeAdmin\Model\Search\Efax\Inbound($this->pixie);
		$search->setUser($this->user);
		$results = $search->search(
			$this->orm->get('Efax_InboundFax'),
			$this->request
		);

		foreach ($results as $result) {
			$items[] = $result
				->getFormatter('WidgetList')
				->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionGetUnreadCount()
	{
		$search = new \OpakeAdmin\Model\Search\Efax\Inbound($this->pixie);
		$search->setUser($this->user);
		$count = $search->getUnreadCount(
			$this->orm->get('Efax_InboundFax')
		);

		$this->result = [
			'count' => $count,
		];
	}

	public function actionGetSitesList()
	{
		$sites = $this->userSites;
		$result = [];
		foreach ($sites as $site) {
			$result[] = $site->getFormatter('FilterOptionsEntry')->toArray();
		}

		$this->result = $result;
	}

	public function actionMarkAsUnread()
	{
		$data = $this->getData();
		if (!empty($data->faxIds)) {
			foreach ($data->faxIds as $faxId) {
				$fax = $this->pixie->orm->get('Efax_InboundFax', $faxId);
				if ($fax->loaded()) {
					if ($this->hasAccessToFax($fax)) {
						$fax->markAsUnreadForUser($this->user);
					}
				}
			}
		}

		$this->result = [
			'success' => true
		];
	}

	public function actionGetDocumentLink()
	{
		$faxId = $this->request->param('subid');
		$fax = $this->pixie->orm->get('Efax_InboundFax', $faxId);
		if ($fax->loaded()) {
			if (!$this->hasAccessToFax($fax)) {
				throw new Forbidden();
			}

			if (!$fax->saved_file_id) {
				$faxService =  new \OpakeAdmin\Service\Scrypt\SFax\FaxService();
				$faxService->downloadInboundFax($fax);
			}
		}

		$this->result = [
			'success' => true,
		    'preview_url' => '/efax/viewFax/' . $fax->id(),
		    'download_url' => '/efax/viewFax/' . $fax->id() . '?download=1',
		    'mime_type' => 'application/pdf'
		];
	}

	public function actionCompileFaxes()
	{
		$documents = $this->request->post('documents');
		$faxes = [];
		if (!is_array($documents)) {
			throw new BadRequest('Bad Request');
		}
		foreach ($documents as $documentId) {
			$fax = $this->pixie->orm->get('Efax_InboundFax', $documentId);
			if ($fax->loaded()) {
				if ($this->hasAccessToFax($fax)) {
					$faxes[] = $fax;
				}
			}
		}

		if (!$faxes) {
			throw new BadRequest('Faxes array is empty');
		}

		foreach ($faxes as $fax) {
			$fax->markAsReadForUser($this->user);
			if (!$fax->saved_file_id) {
				$faxService =  new \OpakeAdmin\Service\Scrypt\SFax\FaxService();
				$faxService->downloadInboundFax($fax);
			}
		}

		$documentsToPrint = [];
		foreach ($faxes as $fax) {
			$documentsToPrint[] = new UploadedFileDocument($fax->saved_file);
		}

		$printCompiler = new PrintCompiler();
		$printResult = $printCompiler->compile($documentsToPrint);

		$this->result = [
			'success' => true,
			'id' => $printResult->id(),
			'url' => $printResult->getResultUrl()
		];
	}

	protected function hasAccessToFax($fax)
	{
		$siteId = $fax->site_id;
		foreach ($this->userSites as $site) {
			if ($siteId == $site->id()) {
				return true;
			}
		}

		return false;
	}

	protected function iniUser()
	{
		$user = $this->logged();
		if (!$user) {
			throw new \Opake\Exception\Forbidden('Not authorized');
		}

		$this->user = $user;
		$this->userSites = $user->sites->find_all()->as_array();
		$this->org = $user->organization;
	}
}