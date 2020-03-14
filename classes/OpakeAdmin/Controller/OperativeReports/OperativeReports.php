<?php

namespace OpakeAdmin\Controller\OperativeReports;

use Opake\Model\Role;

class OperativeReports extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));
		$user = $this->logged();

		$this->view->addBreadCrumbs(['/operative-reports/' . $this->org->id => 'Surgeon Templates']);
		if (!$user->isInternal() && ($user->role_id == Role::Doctor || $user->role_id == Role::FullClinical)) {
			$this->view->setActiveMenu('settings.templates.operative-report');
		} else {
			$this->view->setActiveMenu('settings.templates.surgeon-templates');
		}

		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$user_id = $this->request->param('subid');

		if ($this->logged()->role_id == Role::Doctor) {
			$user_id = $this->logged()->id;
		}

		$this->checkAccess('surgeon_templates', 'index');
		if ($this->logged()->role_id == Role::Doctor || $user_id) {
			$this->view->user_id = $user_id;
			$user = $this->orm->get('User', $user_id);
			if ($user->loaded()) {
				$this->view->user = $user;
			}
			$this->view->subview = 'operative-report/surgeon-template/index';
		} else {
			$this->view->subview = 'operative-report/surgeon-template/surgeons';
		}
	}

	public function actionView()
	{
		$user_id = $this->request->param('userid');

		$template = $this->orm->get('Cases_OperativeReport_Future', $this->request->param('subid'));

		if (!$template->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$this->checkAccess('surgeon_templates', 'view', $template);
		if ($user_id) {
			$this->view->user_id = (int)$user_id;
		}
		$this->view->id = $template->id;
		$this->view->template = $template;
		$this->view->addBreadCrumbs(['' => 'View Operative Report Template']);
		$this->view->subview = 'operative-report/surgeon-template/view';
		$this->iniDictation();
	}

	public function actionSiteTemplate()
	{
		if (!$this->getAccessLevel('operative_reports', 'view')->isAllowed()) {
			throw new \Opake\Exception\Forbidden();
		}
		$this->view->addBreadCrumbs(['/operative-reports/' . $this->org->id . '/siteTemplate/' => 'Site Op Report']);
		$this->view->setActiveMenu('settings.templates.site-template');
		$this->view->subview = 'operative-report/site-template/index';
	}


}