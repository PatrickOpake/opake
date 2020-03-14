<?php

namespace OpakeApi\Service\Cases;


use OpakeApi\Model\Cases\OperativeReport\SiteTemplate;
use Opake\Service\Cases\OperativeReports as OpakeOperativeReports;

class OperativeReports extends OpakeOperativeReports
{

	public function getTemplate($org_id)
	{
		$template = [];
		$template_items = $this->pixie->orm->get('Cases_OperativeReport_SiteTemplate')->where('organization_id', $org_id);
		foreach ($template_items->find_all() as $item) {
			$template[$item->field] = $item->toArray();
		}
		if (empty($template)) {
			foreach (SiteTemplate::getFields() as $key => $val) {
				$template[$key] = [
					'id' => null,
					'organization_id' => $org_id,
					'group_id' => SiteTemplate::getGroupIdByField($key),
					'field' => $key,
					'type' => SiteTemplate::getTypeByField($key),
					'name' => SiteTemplate::getNameByField($key),
					'sort' => SiteTemplate::getSortByField($key),
					'active' => $val,
				];
			}
		}
		return $template;
	}

	public function getFieldsTemplate($org_id, $report_id)
	{
		$result = [];
		if ($report_id) {
			$fieldsTemplate = $this->orm->get('Cases_OperativeReport_ReportTemplate')->where('report_id', $report_id);
			foreach ($fieldsTemplate ->find_all() as $item) {
				$result[$item->field] = $item->toArray();
			}
		}
		if (empty($result)) {
			$result = $this->getTemplate($org_id);
		}
		return $result;
	}

	public function getCustomFieldsTemplate($org_id, $report_id)
	{
		$result = [];
		$reportTemplates = $this->orm->get('Cases_OperativeReport_ReportTemplate')
			->where('report_id', $report_id)
			->find();
		if ($reportTemplates->loaded()) {
			$reportTemplates = $this->orm->get('Cases_OperativeReport_ReportTemplate')
				->where('report_id', $report_id)
				->where('field', 'custom');

			foreach ($reportTemplates->find_all() as $reportTemplate) {
				$result[] = $reportTemplate->toArray();
			}
		} else {
			$result = $this->getSiteCustomFields($org_id);
		}

		return $result;
	}

	public function saveReportTemplate($org_id, $data, $case)
	{
		$reportTemplateModel = $this->orm->get('Cases_OperativeReport_ReportTemplate')
			->where('report_id', $data->reportid);
		$this->beginTransaction();
		try {
			if(!empty($reportTemplateModel->count_all())) {
				$saveFields = function($fieldItem, $templateFromDB) use($case) {
					$templateFromDB->active = (int)$fieldItem->field->active;
					if($fieldItem->field->field === 'custom') {
						$templateFromDB->custom_value = $fieldItem->value;
					}
					$templateFromDB->updateDynamicVariables($case);
					$templateFromDB->save();
				};
				foreach($reportTemplateModel->find_all() as $templateField) {
					foreach ($data->fields as $fieldItem) {
						if($fieldItem->field->field === 'staff') {
							foreach ($fieldItem->value as $staffField) {
								if($staffField->field->field === $templateField->field) {
									$saveFields($staffField, $templateField);
									break;
								}
							}
						}
						if(!array_key_exists($fieldItem->field->field, SiteTemplate::$additionalAPIFields)) {
							if($fieldItem->field->field === $templateField->field && $fieldItem->field->field !== 'custom') {
								$saveFields($fieldItem, $templateField);
								break;
							}
						}
					}
				}
				foreach ($data->fields as $fieldItem) {
					if($fieldItem->field->field === 'custom') {
						$model = $this->orm->get('Cases_OperativeReport_ReportTemplate')
							->where('name', $fieldItem->field->name)
							->where('field', 'custom')
							->where('report_id', $data->reportid)
							->find();
						if(!$model->loaded()) {
							$model = $this->orm->get('Cases_OperativeReport_ReportTemplate');
						}
						$model->report_id = $data->reportid;
						if($fieldItem->field->field === 'custom') {
							$model->custom_value = $fieldItem->value;
						}
						$model->fill($fieldItem->field);
						$model->updateDynamicVariables($case);
						$model->save();
					}
				}
			} else {
				$saveFields = function($fieldItem, $org_id, $reportid) use($case) {
						$model = $this->orm->get('Cases_OperativeReport_ReportTemplate');
						$fieldItem->field->report_id = $reportid;
						$fieldItem->field->id = null;
						if($fieldItem->field->field === 'custom') {
							$model->custom_value = $fieldItem->value;
						}
						$model->fill($fieldItem->field);
						$model->updateDynamicVariables($case);
						$model->save();
				};
				foreach($data->fields as $fieldItem) {
					if($fieldItem->field->field === 'staff') {
						foreach ($fieldItem->value as $staffField) {
							$saveFields($staffField, $org_id, $data->reportid);
						}
					}
					if(!array_key_exists($fieldItem->field->field, SiteTemplate::$additionalAPIFields)) {
						$saveFields($fieldItem, $org_id, $data->reportid);
					}
				}
			}
		} catch (\Exception $e) {
			$this->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}
		$this->commit();
	}

}
