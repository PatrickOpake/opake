<?php

namespace OpakeAdmin\Controller\Analytics;

use Opake\ActivityLogger\ActivityExporter;
use OpakeAdmin\Controller\AuthPage;
use OpakeAdmin\Model\Search\Analytics\UserActivity;

class Analytics extends AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/analytics/' . $this->org->id => 'Analytics']);
		$this->view->setActiveMenu('settings.analytics');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('analytics', 'view');
		$this->redirect('/analytics/' . $this->org->id . '/userActivity');
	}

	public function actionUserActivity()
	{
		$this->checkAccess('analytics', 'view');
		$this->view->setActiveMenu('analytics.user-activity');
		$this->view->subview = 'analytics/user-activity/index';
		$this->view->isInternal = false;
	}

	public function actionExportUserActivity()
	{
		$model = $this->orm->get('Analytics_UserActivity_ActivityRecord');

		$search = new UserActivity($this->pixie, false);
		$results = $search->search($model, $this->request);

		$exporter = new ActivityExporter($this->pixie);
		$exporter->setShowOrganization(false);
		$exporter->setModels($results);
		$exporter->setFiltersFromRequest($this->request);
		$exporter->exportToExcel();

		$now = new \DateTime();

		$this->response->file(
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'User_Activity_' . $now->format('Y-m-d_h-i-s') . '.xlsx',
			$exporter->getOutput()
		);

		$this->view = null;
	}

}
