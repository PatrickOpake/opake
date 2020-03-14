<?php

namespace OpakeAdmin\Controller\FeeSchedule;

use Opake\Exception\BadRequest;

class FeeSchedule extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(array('/fee-schedule/' . $this->org->id => 'Fee Schedule'));
		$this->view->setActiveMenu('settings.databases.fee-schedule');
		$this->view->set_template('inner');
	}

	public function actionView()
	{
		$this->checkAccess('sites', 'view');
		$siteId = $this->request->param('subid');
		\OpakeAdmin\Helper\Menu\SiteNavigationMenu::prepareMenu($this->view, $siteId);
		$this->view->setActiveMenu('settings.organization.sites.fee-schedule');

		$this->view->siteId = $siteId;
		$this->view->subview = 'clients/sites/fee-schedule/index';
	}

	public function actionDownloadFeeSchedule()
	{
		$this->checkAccess('sites', 'view');
		$siteId = $this->request->param('subid');
		$type = $this->request->get('type');

		$exporter = new \OpakeAdmin\Helper\Export\FeeSchedule();
		$exporter->setSiteId($siteId);
		$exporter->setType($type);
		$content = $exporter->generateCsv();

		$this->response->file('text/csv', 'Fee Schedule.csv', $content);

		$this->execute = false;
	}
}
