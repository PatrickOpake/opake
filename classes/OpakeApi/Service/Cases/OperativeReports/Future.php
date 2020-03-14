<?php

namespace OpakeApi\Service\Cases\OperativeReports;

use Opake\ActivityLogger\Extractor\PrefCard\PrefCardExtractor;
use Opake\Model\Cases\OperativeReport;
use Opake\Service\Cases\OperativeReports\Future as OpakeOperativeReportsFuture;
use OpakeApi\Model\Cases\OperativeReport\SiteTemplate;

class Future extends OpakeOperativeReportsFuture
{

	public function getStaffInfoArray($template)
	{
		$data = [
			['field' => $template['surgeon'],  'value' => []],
			['field' => $template['other_staff'],  'value' => []],
			['field' => $template['co_surgeon'], 'value' => []],
			['field' => $template['supervising_surgeon'], 'value' => []],
			['field' => $template['first_assistant_surgeon'], 'value' => []],
			['field' => $template['assistant'], 'value' => []],
			['field' => $template['anesthesiologist'], 'value' => []],
			['field' => $template['dictated_by'], 'value' => []]
		];

		return $data;
	}

	public function findFutureReports($case, $surgeon_id)
	{
		$op_report_service = $this->pixie->services->get('cases_operativeReports');
		$futureReports = [];
		$model = $this->orm->get('Cases_OperativeReport_Future');
		$model->query
			->fields('case_op_report_future.*')
			->group_by($model->table . '.id');

		$model->query->join(['case_op_report_future_user', 'u'], ['case_op_report_future.id', 'u.report_id']);
		$model->where('u.user_id', $surgeon_id);

		foreach ($model->find_all() as $futureReportModel) {
			if ($futureReportModel->loaded()) {
				$data = [];
				$data['fields'] = [];
				$data['id'] =  $futureReportModel->id();
				$data['organization_id'] =  $futureReportModel->organization_id;
				$data['name'] =  $futureReportModel->name;
				$futureReport = $futureReportModel->toArray();

				$futureCustomFieldValues = $this->getFutureCustomFieldValues($case->organization_id, $futureReport['id']);

				$data['fields'] = array_merge($data['fields'], $futureCustomFieldValues);
				$template = [];
				$fieldsTemplateModel = $this->orm->get('Cases_OperativeReport_Future_Template')->where('future_template_id', $futureReport['id']);
				foreach ($fieldsTemplateModel->find_all() as $item) {
					$template[$item->field] = $item->toArray();
				}
				if (empty($template)) {
					$template = $op_report_service->getTemplate($case->organization_id);
					foreach ($template as $key => $item) {
						$item['future_template_id'] = $futureReportModel->id();
						$template[$key] = $item;
					}
				}
				foreach($futureReport as $fieldName => $fieldValue) {
					if(isset($template[$fieldName])) {
						$data['fields'][] = ['field' => $template[$fieldName], 'value' => $fieldValue];
					}
				}
				$template['staff'] = [
					'id' => null,
					'organization_id' => $futureReportModel->organization_id,
					'future_template_id' => $futureReportModel->id,
					'group_id' => SiteTemplate::getGroupIdByField('staff'),
					'field' => 'staff',
					'type' => SiteTemplate::getTypeByField('staff'),
					'name' => SiteTemplate::getNameByField('staff'),
					'sort' => SiteTemplate::getSortByField('staff'),
					'show' => SiteTemplate::getShowByField('staff'),
					'active' => true,
				];
				$data['fields'][] = ['field' => $template['staff'], 'value' => $this->getStaffInfoArray($template)];
				$data['fields'][] = ['field' => $template['pre_op_diagnosis'], 'value' => []];
				$data['fields'][] = ['field' => $template['post_op_diagnosis'], 'value' => []];
				$data['fields'][] = ['field' => $template['operation_time'], 'value' => ''];

				foreach ($data['fields'] as $key => $item) {
					$item['field']['future_template_id'] = $futureReportModel->id();
					$data['fields'][$key] = $item;
				}
				//$data['fields'][] = ['field' => $template['procedure'], 'value' => ''];
				$futureReports[] = $data;
			}
		}

		return $futureReports;
	}

	public function getFutureCustomFieldValues($org_id, $future_id) {
		$result = [];
		foreach ($this->getFutureAndSiteTemplates($org_id, $future_id) as $fieldTemplate) {

			$fieldValue = [];
			$customValue = '';
			if(isset($fieldTemplate['custom_value'])) {
				$customValue = $fieldTemplate['custom_value'];
			}
			$fieldValue['value'] = $customValue;
			$fieldValue['field'] = $fieldTemplate;
			$result[] = $fieldValue;
		}
		return $result;
	}

}
