<?php

namespace Opake\Service\Cases;


use Opake\Model\Cases\OperativeReport\SiteTemplate;

class OperativeReports extends \Opake\Service\AbstractService
{

	protected $base_model = 'Cases_Operative_Report';

	public function getTemplate($org_id)
	{
		$template = [];
		$template_items = $this->orm->get('Cases_OperativeReport_SiteTemplate')
			->where('organization_id', $org_id)
			->order_by('group_id', 'asc')
			->order_by('sort', 'asc');
		foreach ($template_items->find_all() as $item) {
			$template[$item->group_id][] = $item->toArray();
		}
		if (empty($template)) {
			foreach (SiteTemplate::getFields() as $key => $val) {
				$group_id = SiteTemplate::getGroupIdByField($key);
				if($group_id) {
					$template[$group_id][] = [
						'id' => null,
						'organization_id' => $org_id,
						'group_id' => $group_id,
						'field' => $key,
						'type' => SiteTemplate::getTypeByField($key),
						'name' => SiteTemplate::getNameByField($key),
						'sort' => SiteTemplate::getSortByField($key),
						'show' => SiteTemplate::getShowByField($key),
						'active' => $val,
					];
				}
			}
			foreach($template as &$group) {
				usort($group, function($a, $b) {
					return $a['sort']>$b['sort'];
				});
			}
		}
		return $template;
	}

	public function getSiteCustomFields($org_id)
	{
		$template = [];
		$template_items = $this->orm->get('Cases_OperativeReport_SiteTemplate')
			->where('organization_id', $org_id)
			->where('field', 'custom');
		foreach ($template_items->find_all() as $item) {
			$template[] = $item->toArray();
		}

		return $template;
	}

	public function getFieldsTemplate($org_id, $report_id)
	{
		$result = [];
		if ($report_id) {
			$fieldsTemplate = $this->orm->get('Cases_OperativeReport_ReportTemplate')
				->where('report_id', $report_id)
				->order_by('group_id', 'asc')
				->order_by('sort', 'asc');
			foreach ($fieldsTemplate->find_all() as $item) {
					$result[$item->group_id][] = $item->toArray();
			}
		}
		if(empty($result)) {
			$result = $this->getTemplate($org_id);
		}
		return $result;
	}
	
	public function saveAmendment($report_id, $user_id,  $data)
	{
		if(isset($data->amendment_text) && $data->amendment_text) {
			$model = $this->orm->get('Cases_OperativeReport_Amendment');
			$model->report_id = $report_id;
			$model->time_signed = strftime('%Y-%m-%d %H:%M:%S');
			$model->user_signed = $user_id;
			$model->text = $data->amendment_text;
			$model->save();
		}
	}

	/**
	 * @return bool
	 */
	public function isSiteTemplateCustomField($name, $org_id)
	{
		$siteTemplateModel = $this->orm->get('Cases_OperativeReport_SiteTemplate')
			->where('field', 'custom')
			->where('name', $name)
			->where('organization_id', $org_id)
			->find();
		return $siteTemplateModel->loaded();
	}

	public function updateReportsActivity($data, $org_id)
	{
		$model = $this->orm->get('Cases_OperativeReport');
		$model->query->join('case', ['case.id', $model->table . '.case_id'])
			->fields($model->table . '.*')
			->where('is_exist_template', 0)
			->where('case.organization_id', $org_id);

		foreach ($model->find_all() as $report) {
			foreach ($data as $item) {
				if($report->type == $item->field) {
					$report->is_active = $item->active;
					$report->save();
				}
			}
		}
	}

}
