<?php

namespace OpakeAdmin\Controller\Analytics;

use Opake\ActivityLogger\ActivityExporter;
use OpakeAdmin\Controller\AuthPage;
use OpakeAdmin\Model\Search\Analytics\UserActivity;

class Internal extends AuthPage
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->setBreadcrumbs(['/analytics/internal' => 'Analytics']);
		$this->view->topMenuActive = 'analytics';
	}


	public function actionIndex()
	{
		$this->redirect('/analytics/internal/userActivity');
	}

	public function actionUserActivity()
	{
		$this->view->subview = 'analytics/user-activity/index';
		$this->view->isInternal = true;
	}

	public function actionExportUserActivity()
	{
		$model = $this->orm->get('Analytics_UserActivity_ActivityRecord');

		$search = new UserActivity($this->pixie, false);
		$results = $search->search($model, $this->request);

		$exporter = new ActivityExporter($this->pixie);
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
