<?php

namespace OpakeAdmin\Controller\Efax;

use Opake\Exception\FileNotFound;
use Opake\Exception\Forbidden;

class Efax extends \OpakeAdmin\Controller\AuthPage
{
	protected $user;
	protected $userSites;

	public function before()
	{
		parent::before();
		$this->iniUser();
	}

	public function actionViewFax()
	{
		$faxId = $this->request->param('subid');
		$download = $this->request->param('download');

		/** @var \Opake\Model\Efax\InboundFax $fax */
		$fax = $this->pixie->orm->get('Efax_InboundFax', $faxId);

		if (!$this->hasAccessToFax($fax)) {
			throw new Forbidden();
		}

		$fax->markAsReadForUser($this->user);

		if (!$fax->loaded()) {
			throw new FileNotFound();
		}

		$file = $fax->saved_file;

		if (!$file->loaded()) {
			throw new FileNotFound();
		}

		$this->response->file('application/pdf', 'fax-' . $fax->to_fax . '-' . $fax->id() . '.pdf', $file->readContent(), $download);
		$this->execute = false;
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