<?php

namespace OpakeApi\Controller;

use Opake\Model\Cases\OperativeReport;

class Reports extends AbstractController
{

	/**
	 * Service for OperativeReports
	 * @var \Opake\Service\Cases\OperativeReports
	 */
	protected $service;

	public function __construct($pixie)
	{
		parent::__construct($pixie);
		$this->service = $this->services->get('cases_operativeReports');
	}

	public function actionMyreports()
	{
		$search = new \OpakeApi\Model\Search\Cases\OperativeReport($this->pixie);
		$results = $search->search($this->request);

		$reports = [];
		foreach ($results as $report) {
			$reports[] = $report->toShortArray();
		}

		$this->result = [
			'reports' => $reports,
			'total_count' => $search->getCountByAlert(),
		];
	}

	public function actionChangestatus()
	{
		$status = $this->request->get('status');
		$report = $this->loadModel('Cases_OperativeReport', 'reportid');
		if($status != OperativeReport::STATUS_DRAFT
			&& $status != OperativeReport::STATUS_OPEN
			&& $status != OperativeReport::STATUS_SUBMITTED) {
			throw new \OpakeApi\Exception\BadRequest('Unknown status');
		}
		if ($report->loaded()) {
			$report->status = $status;
			$report->save();
		}
		$this->result = ['success' => true];
	}

	public function actionTemplates()
	{
		$service_future = $this->services->get('cases_operativeReports_Future');
		$case = $this->loadModel('Cases_Item', 'caseid');

		$loggedUser = $this->pixie->auth->user();
		$opReport = $case->getOpReport(true, $loggedUser->id());

		$futureReports = $service_future->findFutureReports($case, $opReport->surgeon_id);

		$this->result = [
			'templates' => $futureReports
		];
	}

}
