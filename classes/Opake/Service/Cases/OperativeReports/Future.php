<?php

namespace Opake\Service\Cases\OperativeReports;


use Opake\Model\Cases\OperativeReport;
use Opake\Model\Cases\OperativeReport\SiteTemplate;
use Opake\Model\Profession;

class Future extends \Opake\Service\AbstractService
{

	protected $base_model = 'Cases_Operative_Report';

	public function getFutureAndSiteTemplates($org_id, $future_id)
	{
		$op_report_service = $this->pixie->services->get('cases_operativeReports');

		$result = [];
		$reportTemplates = $this->orm->get('Cases_OperativeReport_Future_Template')
			->where('future_template_id', $future_id)
			->where('field', 'custom');

		foreach ($reportTemplates->find_all() as $reportTemplate) {
			$result[] = $reportTemplate->toArray();
		}
		foreach ($op_report_service->getSiteCustomFields($org_id) as $siteTemplate) {
			$key = array_search($siteTemplate['name'], array_column($result, 'name'));
			if($key === false) {
				$result[] = $siteTemplate;
			}
		}

		return $result;
	}

	public function getFutureTemplate($org_id, $future) {
		$op_report_service = $this->pixie->services->get('cases_operativeReports');
		$result = [];
		$fieldsTemplateModel = $this->orm->get('Cases_OperativeReport_Future_Template')
			->where('future_template_id', $future->id())
			->order_by('group_id', 'asc')
			->order_by('sort', 'asc');
		foreach ($fieldsTemplateModel->find_all() as $item) {
			$result[$item->group_id][] = $item->toArray();
		}
		if (empty($result)) {
			$result = $op_report_service->getTemplate($org_id);
		}

		return $result;
	}

	public function saveForFuture($op_report, $data, $template)
	{
		$case = $op_report->case;
		if ($case->loaded()) {
			$users = [];
			if (isset($case->users) && $case->users) {
				foreach ($case->users->find_all() as $user) {
					$users[] = $user->id;
				}
			}

			$model = $this->findFutureReportByName($data->template_name);
			if ($model->loaded()) {
				$this->removeFutureUsers($model->id);
			} else {
				$model = $this->pixie->orm->get('Cases_OperativeReport_Future');
			}

			$model->fromOpReport($op_report);
			$model->name = $data->template_name;

			$model->save();
			foreach ($users as $user) {
				$this->addUserToFuture($user, $model->id);
			}
			$this->beginTransaction();
			try {
				$this->orm->get('Cases_OperativeReport_Future_Template')->where('future_template_id', $model->id())->delete_all();
				foreach($template as $group_id => $fields) {
					foreach($fields as $key => $field) {
						$templateModel = $this->orm->get('Cases_OperativeReport_Future_Template');
						if($field['field'] === 'custom') {
							$templateModel->name = $field['name'];
						}
						$templateModel->future_template_id = $model->id();
						$templateModel->field = $field['field'];
						$templateModel->active = $field['active'] === 'true'? 1: 0;
						$templateModel->group_id = $group_id;
						$templateModel->sort = $key;
						$templateModel->save();
					}
				}
			} catch (\Exception $e) {
				$this->rollback();
				throw new \Exception($e->getMessage());
			}
			$this->commit();

		}
	}

	public function addUserToFuture($user_id, $report_id)
	{
		$this->db->
		query('insert')->
		table('case_op_report_future_user')->
		data(['report_id' => $report_id, 'user_id' => $user_id])->
		execute();
	}

	public function findFutureReports($case, $surgeon_id)
	{
		$futureReports = [];
		$op_report_service = $this->pixie->services->get('cases_operativeReports');

		$model = $this->orm->get('Cases_OperativeReport_Future');
		$model->query
			->fields('case_op_report_future.*')
			->group_by($model->table . '.id');

		$model->query->join(['case_op_report_future_user', 'u'], ['case_op_report_future.id', 'u.report_id']);
		$model->where('u.user_id', $surgeon_id);

		foreach ($model->find_all() as $futureReportModel) {
			if ($futureReportModel->loaded()) {

				$futureReport = $futureReportModel->toArray();

				$template = [];
				$fieldsTemplateModel = $this->orm->get('Cases_OperativeReport_Future_Template')->where('future_template_id', $futureReport['id']);
				foreach ($fieldsTemplateModel ->find_all() as $item) {
					$template[$item->group_id][] = $item->toArray();
				}
				if (empty($template)) {
					$template = $op_report_service->getTemplate($case->organization_id);
				}
				$futureReport['template'] = $template;
				$futureReports[] = $futureReport;
			}
		}

		return $futureReports;
	}

	public function findFutureReportByName($name)
	{
		$model = $this->orm->get('Cases_OperativeReport_Future');
		return $model->where('name', $name)
			->find();
	}

	public function removeFutureUsers($report_id)
	{
		$this->pixie->db->query('delete')->table('case_op_report_future_user')->where('report_id', $report_id)->execute();
	}

}
