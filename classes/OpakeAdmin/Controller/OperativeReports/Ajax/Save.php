<?php

namespace OpakeAdmin\Controller\OperativeReports\Ajax;

use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Cases\OperativeReport;
use Opake\Model\Cases\OperativeReport\SiteTemplate;
use Opake\Model\Profession;

class Save extends \OpakeAdmin\Controller\Ajax {

	/**
	 * @throws \Opake\Exception\Forbidden
	 * @throws \Opake\Exception\PageNotFound
	 */
	public function before() {
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	/**
	 * @throws \Exception
	 * @throws \Opake\Exception\Ajax
	 */
	public function actionTemplate() {
		$data = $this->getData();
		$service = $this->services->get('cases_operativeReports');

		$service->beginTransaction();
		$this->orm->get('Cases_OperativeReport_SiteTemplate')->where('organization_id', $this->org->id)->delete_all();
		try {
			$caseInfoGroup = [];
			foreach($data as $group_id => $fields) {
				foreach($fields as $key => $field) {
					$model = $this->orm->get('Cases_OperativeReport_SiteTemplate');
					$field->organization_id = $this->org->id;
					$field->group_id = $group_id;
					$field->sort = $key;
					$this->updateModel($model, $field);
				}
				if($group_id == SiteTemplate::GROUP_CASE_INFO_ID) {
					$caseInfoGroup = $fields;
				}
			}
			$service->updateReportsActivity($caseInfoGroup, $this->org->id);
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$service->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}
		$service->commit();
		$this->result = ['status' => 'ok'];
	}

	/**
	 * @throws \Exception
	 * @throws \Opake\Exception\Ajax
	 */
	public function actionReport() {
		$data = $this->getData();
		$isFuture = $this->request->post('future');
		$source = $this->request->post('source');
		$template = $this->request->post('template');
		$service = $this->services->get('cases_operativeReports');
		$service_future = $this->services->get('cases_operativeReports_Future');
		$isNotBeginReport = false;

		if (isset($data->id)) {
			$model = $this->orm->get('Cases_OperativeReport', $data->id);
			$isNotBeginReport = $model->status == OperativeReport::STATUS_OPEN;
		} else {
			$model = $this->orm->get('Cases_OperativeReport');
		}
		$service->beginTransaction();
		try {
			if ($data) {
				$model->fill($data);
			}

			foreach ($template[SiteTemplate::GROUP_CASE_INFO_ID] as $t) {
				if($model->type == $t['field']) {
					if($t['active'] == 'true') {
						$model->is_active = 1;
					} else {
						$model->is_active = 0;
					}
					break;
				}
			}
			$model->is_exist_template = 1;

			$this->checkValidationErrors($model);

			$actionQueue = $this->pixie->activityLogger->newModelActionQueue($model);
			if ($source === 'cm') {
				$actionQueue->addAction(ActivityRecord::ACTION_CLINICAL_EDIT_OP_REPORT);
			} else {
				$actionQueue->addAction(ActivityRecord::ACTION_EDIT_OP_REPORT_TEMPLATES);
			}
			if($model->status == OperativeReport::STATUS_SUBMITTED) {
				$actionQueue->addAction(ActivityRecord::ACTION_OP_REPORT_SUBMITTED);
			}
			if($model->status == OperativeReport::STATUS_DRAFT && $isNotBeginReport) {
				$actionQueue->addAction(ActivityRecord::ACTION_OP_REPORT_BEGIN);
			}
			$actionQueue->assign();

			$model->save();

			if ($isFuture == 'true') {
				$service_future->saveForFuture($model, $data, $template);
			}
			if($model->status == OperativeReport::STATUS_SIGNED) {
				$this->pixie->activityLogger->newModelActionQueue($model)
					->addAction(ActivityRecord::ACTION_OP_REPORT_AMENDED)
					->assign();
				$service->saveAmendment($model->id(), $this->logged()->id(), $data);
			}


			$actionQueue->registerActions();

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$service->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}
		$service->commit();
		$this->result = ['id' => (int) $model->id];
	}

	/**
	 * @throws \Opake\Exception\Ajax
	 */
	public function actionFutureReport()
	{
		$data = $this->getData();
		if (isset($data->id)) {
			$model = $this->orm->get('Cases_OperativeReport_Future', $data->id);
		} else {
			$model = $this->orm->get('Cases_OperativeReport_Future');
		}
		try {
			if (isset($data->user_id)) {
				$user = $this->orm->get('User', $data->user_id);
				$data->surgeons[] = $user;
			}

			if ($data) {
				$model->fill($data);
			}

			$actionQueue = $this->pixie->activityLogger->newModelActionQueue($model);
			if (!$model->loaded()) {
				$actionQueue->addAction(ActivityRecord::ACTION_SETTINGS_ADD_OPERATIVE_REPORT_TEMPLATE);
			} else {
				$actionQueue->addAction(ActivityRecord::ACTION_SETTINGS_EDIT_OPERATIVE_REPORT_TEMPLATE);
			}
			$actionQueue->assign();

			$this->checkValidationErrors($model);
			$model->save();

			if (isset($data->case_type) && $data->case_type->id) {
				$caseType = $this->orm->get('Cases_type', $data->case_type->id);
				$model->case_type = $caseType;
			}

			$actionQueue->registerActions();

		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = ['id' => (int) $model->id];
	}

	/**
	 * @throws \Exception
	 * @throws \Opake\Exception\Ajax
	 * @throws \Opake\Exception\PageNotFound
	 */
	public function actionFieldsReport()
	{
		$service = $this->services->get('cases_operativeReports');
		$data = $this->getData();
		$report = $this->loadModel('Cases_OperativeReport', 'subid');
		$isOnlyValidate = $this->request->post('isOnlyValidate');
		$service->beginTransaction();
		$this->orm->get('Cases_OperativeReport_ReportTemplate')->where('report_id', $report->id())->delete_all();
		try {
			foreach($data as $group_id => $fields) {
				foreach($fields as $key => $field) {
					$model = $this->orm->get('Cases_OperativeReport_ReportTemplate');
					$field->report_id = $report->id();
					$field->group_id = $group_id;
					$field->sort = $key;
					$field->id = null;
					$this->updateModel($model, $field);
				}
			}
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$service->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}
		if($isOnlyValidate) {
			$service->rollback();
		} else {
			$service->commit();
		}

		$this->result = ['id' => (int) $model->id];
	}

	/**
	 * @throws \Exception
	 * @throws \Opake\Exception\Ajax
	 * @throws \Opake\Exception\PageNotFound
	 */
	public function actionFutureFieldsReport()
	{
		$service = $this->services->get('cases_operativeReports');
		$isOnlyValidate = $this->request->post('isOnlyValidate');
		$data = $this->getData();
		$future = $this->loadModel('Cases_OperativeReport_Future', 'subid');

		$service->beginTransaction();
		$this->orm->get('Cases_OperativeReport_Future_Template')->where('future_template_id', $future->id())->delete_all();
		try {
			foreach($data as $group_id => $fields) {
				foreach($fields as $key => $field) {
					$model = $this->orm->get('Cases_OperativeReport_Future_Template');
					$field->id = null;
					$field->future_template_id = $future->id();
					$field->active = isset($field->active) ? $field->active : 0;
					$field->group_id = $group_id;
					$field->sort = $key;
					$this->updateModel($model, $field);
				}
			}

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$service->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}
		if($isOnlyValidate) {
			$service->rollback();
		} else {
			$service->commit();
		}
		$this->result = ['id' => (int) $model->id];
	}
}
