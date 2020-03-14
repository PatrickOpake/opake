<?php

namespace OpakeAdmin\Controller\OperativeReports;

use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Cases\OperativeReport;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	/**
	 * @throws \Opake\Exception\Forbidden
	 * @throws \Opake\Exception\PageNotFound
	 */
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	/**
	 * @throws \Opake\Exception\Forbidden
	 */
	public function actionFutureTemplates()
	{

		$this->checkAccess('operative_reports', 'view');

		$items = [];

		$model = $this->orm->get('Cases_OperativeReport_Future')->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Cases\OperativeReport\Future($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toShortArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	/**
	 * @throws \Exception
	 * @throws \Opake\Exception\PageNotFound
	 */
	public function actionFuture()
	{
		$serviceFuture = $this->services->get('Cases_OperativeReports_Future');
		$model = $this->loadModel('Cases_OperativeReport_Future', 'subid');
		$futureReport = $model->toArray();
		$this->result = [
			'template' => $serviceFuture->getFutureTemplate($this->org->id, $model),
			'future_report' => $futureReport,
		];
	}

	/**
	 * @throws \Exception
	 * @throws \Opake\Exception\PageNotFound
	 */
	public function actionRemoveFutureTemplate()
	{
		$model = $this->loadModel('Cases_OperativeReport_Future', 'subid');
		$model->delete();
	}

	/**
	 *
	 */
	public function actionMyOperativeReports()
	{
		$model = $this->orm->get('Cases_OperativeReport');

		$search = new \OpakeAdmin\Model\Search\Cases\OperativeReport($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toShortArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];

		if ($this->request->get('alerts')) {
			$user_id = null;
			if ($this->request->get('user_id')) {
				$user_id = $this->request->get('user_id');
			}
			$this->result['alerts'] = [
				'open' => $search->getCountByAlert($model, 'open', $user_id),
				'submitted' => $search->getCountByAlert($model, 'submitted', $user_id)
			];
		}
	}

	public function actionOverview()
	{
		$service = $this->services->get('Cases');

		$model = $service->getItem()
			->where('organization_id', $this->org->id)
			->where('stage', '!=', \Opake\Model\Cases\Item::STAGE_BILLING)
			->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);

		$search = new \OpakeAdmin\Model\Search\Cases($this->pixie, false);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toShortArray();
		}

		$this->result = [
			'items' => $items,
		];
	}

	public function actionGenerateNonSurgeonReport()
	{
		$surgeon_id = $this->request->get('surgeon_id');
		$case = $this->loadModel('Cases_Item', 'subid');
		/** @var Cases $service */
		$service = $this->services->get('cases');
		$report_id = $service->createReport($case, OperativeReport::TYPE_NON_SURGEON, $surgeon_id);

		$this->result = (int)$report_id;
	}

	/**
	 *
	 */
	public function actionSurgeons()
	{
		$model = $this->orm->get('User');
		$model->where('organization_id', $this->org->id)
			->where('is_enabled_op_report', true);

//		if ($this->logged()->isFullAdmin() && !$this->logged()->is_enabled_op_report) {
//			$model->where('id', '<>', $this->logged()->id());
//		}

		$search = new \OpakeAdmin\Model\Search\User($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$user = $result->toShortArray();
			$user['report_count'] = $result->getReportCount();
			$user['submitted_report_count'] = $result->getSubmittedReportCount();
			$user['template_count'] = $result->getReportTemplateCount();
			$items[] = $user;
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	/**
	 * @throws \Opake\Exception\PageNotFound
	 */
	public function actionArchive()
	{
		$IDs = $this->request->post('reportIds');
		if (!$IDs || !is_array($IDs)) {
			throw new \Exception('Reports list is empty');
		}

		foreach ($IDs as $id) {
			$op_report = $this->orm->get('Cases_OperativeReport', $id);
			if ($op_report->loaded()) {
				$op_report->is_archived = 1;
				$op_report->save();
			}
		}

		$this->result = ['success' => true];
	}

	/**
	 * @throws \Opake\Exception\PageNotFound
	 */
	public function actionChangeBulkStatus()
	{
		$IDs = $this->request->post('reportIds');
		$status = $this->request->post('status');
		if (!$IDs || !is_array($IDs)) {
			throw new \Exception('Reports list is empty');
		}

		foreach ($IDs as $id) {
			$op_report = $this->orm->get('Cases_OperativeReport', $id);
			if ($op_report->loaded()) {
				$op_report->status = $status;
				$op_report->save();
			}
		}

		$this->result = ['success' => true];
	}

	/**
	 * @throws \Opake\Exception\PageNotFound
	 */
	public function actionChangeStatus()
	{
		$status = $this->request->post('status');
		$op_report = $this->loadModel('Cases_OperativeReport', 'subid');
		$op_report->status = $status;
		$op_report->save();
		$this->result = ['success' => true];
	}

	public function actionSign()
	{
		$model = $this->loadModel('Cases_OperativeReport', 'subid');
		$model->signed_user_id = $this->logged()->id();
		$model->time_signed = strftime('%Y-%m-%d %H:%M:%S');
		$model->status = OperativeReport::STATUS_SIGNED;

		$actionQueue = $this->pixie->activityLogger->newModelActionQueue($model)
			->addAction(ActivityRecord::ACTION_OP_REPORT_SIGN)
			->assign();

		$model->save();

		$actionQueue->registerActions();
		$this->result = ['success' => true];
	}

	/**
	 * @throws \Opake\Exception\PageNotFound
	 */
	public function actionPreview()
	{
		$service = $this->services->get('cases_operativeReports');
		/** @var OperativeReport $report */
		$report = $this->loadModel('Cases_OperativeReport', 'subid');

		$this->result = [
			'report' => $report->toArray(),
			'organization' => $this->org->toArray(),
			'template' => $service->getFieldsTemplate($this->org->id, $report->id()),
		];
	}

	/**
	 * @throws \Exception
	 * @throws \Opake\Exception\PageNotFound
	 */
	public function actionRemoveSiteCustomField() {
		$customField = $this->loadModel('Cases_OperativeReport_SiteTemplate', 'subid');
		$customField->delete();
	}

	/**
	 * @throws \Exception
	 */
	public function actionTemplate()
	{
		$service = $this->services->get('cases_operativeReports');
		$siteTemplate = $service->getTemplate($this->org->id);

		$caseInfo = [];
		foreach($siteTemplate[OperativeReport\SiteTemplate::GROUP_CASE_INFO_ID] as $item) {
			if(OperativeReport\SiteTemplate::getShowByField($item['field']) !== 'only_future') {
				$caseInfo[] = $item;
			}
		}
		$siteTemplate[OperativeReport\SiteTemplate::GROUP_CASE_INFO_ID] = $caseInfo;
		$this->result = $siteTemplate;
	}

	/**
	 *
	 */
	public function actionRemoveFutureCustomField() {
		$toRemoveIds = [];
		$data = $this->getData();
		foreach($data as $customField) {
			if(isset($customField->id)) {
				$toRemoveIds[] = $customField->id;
			}
		}
		if($toRemoveIds) {
			$this->orm->get('Cases_OperativeReport_Future_Template')
				->where('id', 'IN', $this->pixie->db->expr('(' . implode(', ', $toRemoveIds) . ')'))
				->delete_all();
		}
	}

	/**
	 *
	 */
	public function actionRemoveReportCustomField() {
		$toRemoveIds = [];
		$data = $this->getData();
		foreach($data as $customField) {
			if(isset($customField->id)) {
				$toRemoveIds[] = $customField->id;
			}
		}
		if($toRemoveIds) {
			$this->orm->get('Cases_OperativeReport_ReportTemplate')
				->where('id', 'IN', $this->pixie->db->expr('(' . implode(', ', $toRemoveIds) . ')'))
				->delete_all();
		}
	}

}
