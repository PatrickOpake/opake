<?php

namespace OpakeAdmin\Controller\Settings\Databases\InsurancePayors;

use OpakeAdmin\Controller\AuthPage;
use OpakeAdmin\Helper\Export\InsurancesDatabaseExport;
use Opake\Helper\Config;


class Index extends AuthPage
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->initMenu('settings');
		$this->view->setActiveMenu('databases.insurance-payors');
		$this->view->topMenuActive = 'settings';
		$this->view->setBreadcrumbs([
			'/settings/fields/' => 'Settings',
			 '/settings/databases/hcpc' => 'Databases',
			'' => 'Insurances'
		]);
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/databases/insurance-payors/index';
	}

	public function actionDownloadInsuranceDB()
	{
		$model = $this->pixie->orm->get('Insurance_Payor');
		$model->where('actual', 1);

		$search = new \OpakeAdmin\Model\Search\Insurance\Payors($this->pixie, false);
		$results = $search->search($model, $this->request);

		$exporter = new InsurancesDatabaseExport($this->pixie);
		$exporter->setModels($results);
		$exporter->setTemplate($this->pixie->root_dir . Config::get('app.templates.insurance_payor'));
		$exporter->exportToExcel();

		$now = new \DateTime();

		$this->response->file(
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'Insurances_DB_' . $now->format('Y-m-d_h-i-s') . '.xlsx',
			$exporter->getOutput()
		);

		$this->view = null;
	}

}
