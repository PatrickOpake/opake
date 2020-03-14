<?php

namespace OpakeAdmin\Controller\OperativeReports;

use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Profession;
use Opake\Model\Role;

class My extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/operative-reports/my' . $this->org->id => 'Operative Reports']);
		$this->view->setActiveMenu('clinicals.operative-reports');
		$this->view->set_template('inner');
		$this->view->showSideCalendar = true;

		if($this->logged()->isDictation()) {
			$this->view->showSideCalendar = false;
		}
	}

	public function actionIndex()
	{
		$user_id = $this->request->param('subid');

		$this->checkAccess('operative_reports', 'index');

		if(($this->pixie->permissions->checkAccess('operative_reports', 'index_surgeons')) && !$user_id) {
			$this->view->subview = 'operative-report/report/surgeons';
		} else {
			$this->isEnabledOperativeReport($user_id);
			if ($user_id) {
				$this->view->user_id = $user_id;
			}
			$this->view->subview = 'operative-report/report/index';
		}
	}

	public function actionView()
	{
		$this->checkAccess('operative_reports', 'view');
		$user_id = $this->request->param('userid');
		$report = $this->loadModel('Cases_OperativeReport', 'subid');
		$this->view->report = $report;
		$this->isEnabledOperativeReport($user_id);
		if ($user_id) {
			$this->view->user_id = (int)$user_id;
		}
		$this->view->subview = 'operative-report/report/view';
		$this->iniDictation();

		$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_VIEW_OP_REPORT_TEMPLATES)
			->setModel($report)
			->register();

	}

	protected function isEnabledOperativeReport($user_id = null)
	{
		$currentUser = $user = $this->logged();
		if($user_id) {
			$user = $this->orm->get('User', $user_id);
		}
		if (!$user->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}
		if (!$user->is_enabled_op_report && !$currentUser->isInternal() && !$currentUser->isFullAdmin()) {
			throw new \Opake\Exception\Forbidden();
		}
	}

}