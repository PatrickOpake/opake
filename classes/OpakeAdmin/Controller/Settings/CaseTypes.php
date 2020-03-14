<?php

namespace OpakeAdmin\Controller\Settings;

use OpakeAdmin\Helper\Export\ProceduresExport;

class CaseTypes extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/settings/case-types/' . $this->org->id => 'Case Types']);
		$this->view->setActiveMenu('settings.databases.case_types');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/case-types/index';
	}

	public function actionDownload()
	{
		$model = $this->orm->get('Cases_Type')->where('organization_id', $this->org->id)->where('archived', '!=', 1);
		$model->query->order_by('name', 'asc');
		$procedures = $model->find_all()->as_array();

		$export = new ProceduresExport($this->pixie);
		$content = $export->generateExcel($procedures);

		$this->response->file(
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'Procedures_export.xlsx',
			$content
		);

		$this->view = null;
	}
}
