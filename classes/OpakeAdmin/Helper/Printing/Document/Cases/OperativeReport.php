<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases;

use Opake\Model\Cases\OperativeReport\SiteTemplate;
use OpakeAdmin\Helper\Printing\Document;
use OpakeAdmin\Helper\Printing\PrintCompiler;

class OperativeReport extends Document\CompileDocument
{
	/**
	 * @var \Opake\Model\Cases\OperativeReport
	 */
	protected $operativeReport;


	/**
	 * @param \Opake\Model\Cases\OperativeReport $operativeReport
	 */
	public function __construct($operativeReport)
	{
		$this->operativeReport = $operativeReport;
	}

	public function getFileName()
	{
		return 'operative-report-' . $this->operativeReport->getCase()->id() .'.pdf';
	}

	public function getContentMimeType()
	{
		return PrintCompiler::MIME_TYPE_PDF;
	}

	protected function compileContent()
	{
		$app = \Opake\Application::get();

		$service = $app->services->get('Cases_OperativeReports');
		$operativeReport = $this->operativeReport;
		$caseItem = $operativeReport->getCase();

		if (!$operativeReport->loaded()) {
			throw new \Exception('Operative report is not loaded for case ' . $caseItem->id());
		}

		$amendments = [];
		foreach ($operativeReport->amendments->find_all() as $amendment) {
			$amendments[] = $amendment;
		}

		$view = $app->view('cases/export/report');

		$view->case = $caseItem;
		$view->registration = $caseItem->registration;
		$view->organization = $caseItem->organization;
		$view->patient = $caseItem->registration->patient;
		$view->op_report = $operativeReport;
		$view->amendments = $amendments;

		$pre_op_diagnosis = [];
		foreach ($operativeReport->pre_op_diagnosis->find_all() as $diagnosis) {
			$pre_op_diagnosis[] = $diagnosis;
		}
		if (!$pre_op_diagnosis) {
			foreach($operativeReport->getCase()->registration->admitting_diagnosis->find_all() as $diagnosis) {
				$pre_op_diagnosis[] =$diagnosis;
			}
		}
		$view->pre_op_diagnosis = $pre_op_diagnosis;
		$view->post_op_diagnosis = $operativeReport->post_op_diagnosis->find_all()->as_array();
		$surgeons = $caseItem->getSurgeonsArray();
		$template = $service->getFieldsTemplate($caseItem->organization->id, $operativeReport->id());
		$surgeonsArray = [];
		foreach ($template[SiteTemplate::GROUP_CASE_INFO_ID] as $staffField) {
			if(isset($surgeons[$staffField['name']]) && $surgeons[$staffField['name']]) {
				$surgeonsArray[$staffField['name']]['field'] = $staffField;
				$surgeonsArray[$staffField['name']]['value'] = $surgeons[$staffField['name']];
			}
		}
		$view->surgeons = $surgeonsArray;
		$view->template = $template;

		$headerView = $app->view('cases/export/report/header');
		$headerView->case = $caseItem;
		$headerView->organization = $caseItem->organization;
		$headerView->patient = $caseItem->registration->patient;

		$footerView = $app->view('cases/export/report/footer');
		$footerView->op_report = $operativeReport;

		list($pdf, $errors) = \Opake\Helper\Export::createOpReportPdf(
			$app,
			$view->render(),
			$headerView->render(),
			$footerView->render()
		);

		if ($errors) {
			throw new \Exception('PDF generation failed: ' . $errors);
		}

		return $pdf;
	}
}