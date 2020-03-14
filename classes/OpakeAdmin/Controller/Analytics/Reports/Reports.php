<?php

namespace OpakeAdmin\Controller\Analytics\Reports;

use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;
use OpakeAdmin\Controller\AuthPage;

class Reports extends AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/analytics/reports/' . $this->org->id => 'Analytics']);
		$this->view->setActiveMenu('analytics.reports');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('analytics', 'view_reports');
		$this->view->subview = 'analytics/reports/index';
	}

	public function actionDownloadReport()
	{
		$this->checkAccess('analytics', 'view_reports');

		$id = $this->request->get('id');
		if (!$id) {
			throw new BadRequest('ID is required param');
		}
		$key = $this->request->get('key');
		if (!$key) {
			throw new BadRequest('Key is required param');
		}

		$report = $this->pixie->orm->get('Analytics_Reports_GeneratedReport', $id);
		if (!$report->loaded()) {
			throw new PageNotFound('Unknown report');
		}

		if ($key !== $report->key) {
			throw new BadRequest('Invalid key');
		}

		$file = $report->file;
		$this->response->file($file->mime_type, $file->original_filename, $file->readContent(), true);
		$this->execute = false;

		$file->delete();
	}
}