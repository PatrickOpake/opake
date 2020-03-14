<?php

namespace OpakeAdmin\Controller\Analytics\Reports;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;
use Opake\Exception\PageNotFound;
use Opake\Helper\TimeFormat;
use OpakeAdmin\Form\Analytics\Reports\CustomReportForm;
use OpakeAdmin\Helper\Analytics\Reports\CasesReportGenerator;
use OpakeAdmin\Helper\Analytics\Reports\InfectionReportGenerator;
use OpakeAdmin\Helper\Analytics\Reports\RoomUtilizationGenerator;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function actionGenerateReport()
	{
		$this->checkAccess('analytics', 'view_reports');

		$params = $this->request->post('params');
		if (!$params) {
			throw new BadRequest('Bad Request');
		}

		$params = json_decode($params, true);

		$validator = $this->pixie->validate->get($params);
		$validator->field('type')->rule('filled')->error('Report Type is required field');
		$validator->field('organization')->rule('filled')->error('Organization ID is required field');

		if (!$validator->valid()) {
			$this->result = [
				'success' => false,
				'errors' => $validator->common_errors_list()
			];

			return;
		}

		$user = $this->logged();
		if (!$user || (!$user->isInternal() && $params['organization'] != $user->organization_id)) {
			throw new Forbidden();
		}

		$customReport = $customReportType = null;
		if (sscanf($params['type'], 'custom_%d', $customReportId)) {
			$customReport = $this->pixie->orm->get('Analytics_Reports_CustomReport')
				->where('user_id', $user->id())
				->where('id', $customReportId)
				->find();
			if (!$customReport->loaded()) {
				throw new Forbidden();
			}

			$customReportType = $customReport->parent_id;
		}

		if ($params['type'] == InfectionReportGenerator::TYPE_INFECTION_REPORT
			|| $customReportType == InfectionReportGenerator::TYPE_INFECTION_REPORT) {

			if (!isset($params['infectionType'])) {
				$this->result = [
					'success' => false,
					'errors' => ['Infection Type field is required']
				];
				return;
			}

			if (!isset($params['dateFrom'], $params['dateTo'])) {
				$this->result = [
					'success' => false,
					'errors' => ['Date From and Date To are required fields']
				];
				return;
			}

			$reportGenerator = new InfectionReportGenerator($this->pixie);
			$reportGenerator->setDateFrom(TimeFormat::fromDBDate($params['dateFrom']));
			$reportGenerator->setDateTo(TimeFormat::fromDBDate($params['dateTo']));
			$reportGenerator->setOrganizationId($params['organization']);
			$reportGenerator->setInfectionType($params['infectionType']);

			if (!empty($params['surgeons'])) {
				$reportGenerator->setSurgeons($params['surgeons']);
			}

			$report = $reportGenerator->generate();

		}  else if ($params['type'] == RoomUtilizationGenerator::TYPE_ROOM_UTILIZATION
			|| $customReportType == RoomUtilizationGenerator::TYPE_ROOM_UTILIZATION) {

			if (!isset($params['dateFrom'], $params['dateTo'])) {
				$this->result = [
					'success' => false,
					'errors' => ['Date From and Date To are required fields']
				];
				return;
			}

			$reportGenerator = new RoomUtilizationGenerator($this->pixie);
			$reportGenerator->setDateFrom(TimeFormat::fromDBDate($params['dateFrom']));
			$reportGenerator->setDateTo(TimeFormat::fromDBDate($params['dateTo']));
			$reportGenerator->setOrganizationId($params['organization']);

			if (!empty($params['practiceGroups'])) {
				$reportGenerator->setPracticeGroups($params['practiceGroups']);
			}

			if (!empty($params['surgeons'])) {
				$reportGenerator->setSurgeons($params['surgeons']);
			}

			if (!empty($params['locations'])) {
				$reportGenerator->setLocations($params['locations']);
			}

			$report = $reportGenerator->generate();

		} else {

			if (empty($params['columns'])) {
				$this->result = [
					'success' => false,
					'errors' => ['Please select at least one column for export']
				];
				return;
			}

			$reportGenerator = new CasesReportGenerator($this->pixie);
			$reportGenerator->setCustomReport($customReport);
			$reportGenerator->setReportType($params['type']);
			$reportGenerator->setColumns($params['columns']);
			$reportGenerator->setOrganizationId($params['organization']);


			if (!empty($params['practiceGroups'])) {
				$reportGenerator->setPracticeGroups($params['practiceGroups']);
			}

			if (!empty($params['surgeons'])) {
				$reportGenerator->setSurgeons($params['surgeons']);
			}

			if (!empty($params['insurances'])) {
				$reportGenerator->setInsurances($params['insurances']);
			}

			if (!empty($params['dateFrom'])) {
				$reportGenerator->setDateFrom(TimeFormat::fromDBDate($params['dateFrom']));
			}

			if (!empty($params['dateTo'])) {
				$reportGenerator->setDateTo(TimeFormat::fromDBDate($params['dateTo']));
			}

			if (!empty($params['procedures'])) {
				$reportGenerator->setProcedures($params['procedures']);
			}

			if (!empty($params['inventoryItems'])) {
				$reportGenerator->setInventoryItems($params['inventoryItems']);
			}

			if (!empty($params['manufacturers'])) {
				$reportGenerator->setManufacturers($params['manufacturers']);
			}

			if (!empty($params['inventoryItemTypes'])) {
				$reportGenerator->setInventoryItemTypes($params['inventoryItemTypes']);
			}

			if (!empty($params['insuranceTypes'])) {
				$reportGenerator->setInsuranceTypes($params['insuranceTypes']);
			}
			if (!empty($params['billing_status'])) {
				$reportGenerator->setBillingStatuses($params['billing_status']);
			}

			$report = $reportGenerator->generate();
		}

		$this->result = [
			'success' => true,
			'url' => '/analytics/reports/' . $params['organization'] . '/downloadReport/' . '?id=' . $report->id() . '&key=' . $report->key
		];
	}


	public function actionSaveCustom()
	{
		$this->checkAccess('analytics', 'view_reports');

		$currentUser = $this->logged();
		if (!$currentUser) {
			throw new BadRequest('Bad Request');
		}

		$data = $this->request->post('data');
		if (!$data) {
			throw new BadRequest('Bad Request');
		}

		$model = $this->orm->get('Analytics_Reports_CustomReport');

		$model->beginTransaction();
		try {
			$form = new CustomReportForm($this->pixie, $model);
			$form->load($data);
			if ($form->isValid()) {
				$form->save();

				$this->result = [
					'success' => true,
					'id' => $model->id(),
				];
			}
			else {
				$this->result = [
					'success' => false,
					'errors' => $form->getCommonErrorList()
				];
			}
		}
		catch (\Exception $e) {
			$this->logSystemError($e);
			$model->rollback();

			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()],
			];
		}
		$model->commit();
	}


	public function actionDeleteCustom()
	{
		$this->checkAccess('analytics', 'view_reports');

		$rawId = $this->request->post('id');
		if (!$rawId) {
			throw new BadRequest();
		}
		if (!sscanf($rawId, 'custom_%d', $id)) {
			throw new BadRequest();
		}

		$currentUser = $this->logged();
		$model = $this->orm->get('Analytics_Reports_CustomReport');
		$model->beginTransaction();
		$report = $model->where('user_id', $currentUser->id)
			->where('id', (int)$id)
			->find();

		if (!$report->loaded()) {
			$model->rollback();
			throw new PageNotFound();
		}

		$report->delete();
		$model->commit();

		$this->result = [
			'success' => true
		];
	}


	public function actionGetCustom()
	{
		$this->checkAccess('analytics', 'view_reports');

		$currentUser = $this->logged();

		$model = $this->orm->get('Analytics_Reports_CustomReport');
		$model->where('user_id', $currentUser->id)
			->order_by('id');

		$customReports = []; ;
		foreach ($model->find_all() as $report) {
			$customReports[] = $report->toArray();
		}

		$this->result = [
			'success' => true,
			'data' => $customReports,
		];
	}
}