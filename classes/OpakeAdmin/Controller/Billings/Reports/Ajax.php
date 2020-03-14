<?php

namespace OpakeAdmin\Controller\Billings\Reports;

use OpakeAdmin\Helper\Export\BillingCasesReportExport;
use OpakeAdmin\Helper\Export\BillingProceduresReportExport;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionCases()
	{
		$items = [];
		$model = $this->orm->get('Billing_Report_Cases')->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Billing\CasesReport($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionSaveCaseReport()
	{
		$data = $this->getData();

		if (isset($data->id)) {
			$model = $this->orm->get('Billing_Report_Cases', $data->id);
		} else {
			$model = $this->orm->get('Billing_Report_Cases');
		}
		
		try {
			$this->updateModel($model, $data);
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = [
			'id' => (int) $model->id
		];
	}

	public function actionExportCasesReports()
	{
		$model = $this->orm->get('Billing_Report_Cases');

		$search = new \OpakeAdmin\Model\Search\Billing\CasesReport($this->pixie, false);
		$reports = $search->search($model, $this->request);
		$export = new BillingCasesReportExport($this->pixie);
		$csv = $export->generateCsv($reports);

		$this->result = [
			'success' => true,
			'url' => $csv->getWebPath()
		];
	}

	public function actionRemoveCaseReport()
	{
		$caseNote = $this->loadModel('Billing_Report_Cases', 'subid');
		$caseNote->delete();

		$this->result = 'ok';
	}

	public function actionProcedures()
	{
		$items = [];
		$model = $this->orm->get('Billing_Report_Procedures')->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Billing\CasesReport($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionSaveProcedureReport()
	{
		$data = $this->getData();

		if (isset($data->id)) {
			$model = $this->orm->get('Billing_Report_Procedures', $data->id);
		} else {
			$model = $this->orm->get('Billing_Report_Procedures');
		}

		try {
			$this->updateModel($model, $data);
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = [
			'id' => (int) $model->id
		];
	}

	public function actionExportProceduresReports()
	{
		$model = $this->orm->get('Billing_Report_Procedures');

		$search = new \OpakeAdmin\Model\Search\Billing\CasesReport($this->pixie, false);
		$reports = $search->search($model, $this->request);
		$export = new BillingProceduresReportExport($this->pixie);
		$csv = $export->generateCsv($reports);

		$this->result = [
			'success' => true,
			'url' => $csv->getWebPath()
		];
	}

	public function actionRemoveProcedureReport()
	{
		$caseNote = $this->loadModel('Billing_Report_Procedures', 'subid');
		$caseNote->delete();

		$this->result = 'ok';
	}
}
