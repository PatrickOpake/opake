<?php

namespace OpakeAdmin\Controller\Inventory\Report;

use OpakeAdmin\Helper\Export\InventoryExport;

class Report extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(array('/inventory/' . $this->org->id => 'Inventory'));
		$this->view->setActiveMenu('inventory.inventory-report');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'inventory/report';
	}

	public function actionExport()
	{
		$model = $this->orm->get('Inventory')->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Inventory($this->pixie, false);
		$search->setOrganizationId($this->org->id());
		$results = $search->searchCardItems($model, $this->request);

		$exporter = new InventoryExport($this->pixie);
		$exporter->setModels($results);
		if ($filterValues = $this->request->get('filter_values')) {
			$exporter->setFilterValues(json_decode($filterValues, true));
		}
		$content = $exporter->exportToExcel();

		$now = new \DateTime();

		$this->response->file(
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'Inventory_Report_' . $now->format('Y-m-d_h-i-s') . '.xlsx',
			$content
		);

		$this->view = null;
	}
}
