<?php

namespace Opake\Formatter\ActivityLogger;

use Opake\Helper\TimeFormat;

class ActivityListFormatter extends \Opake\Formatter\BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'user_id',
				'user_fullname',
			    'user_org_name',
			    'user_org_id',
			    'case_id',
			    'case_link',
			    'date',
			    'time',
			    'action',
			    'changes',
			    'details'
			],

			'fieldMethods' => [
				'id' => 'int',
			    'user_id' => 'int',
			    'user_org_id' => 'userOrgId',
			    'case_id' => 'deferred',
			    'case_link' => 'deferred',
			    'user_fullname' => 'userFullName',
			    'user_org_name' => 'userOrgName',
				'date' => 'actionDate',
			    'time' => 'actionTime',
			    'action' => 'actionTitle',
			    'changes' => 'actionChanges',
			    'details' => 'actionDetails'
			]

		]);
	}

	/**
	 * @param array $data
	 * @param $fields
	 * @return mixed
	 */
	protected function prepareDeferredData($data, $fields)
	{
		if (in_array('case_id', $fields) || in_array('case_link', $fields)) {
			$row = $this->pixie->db->query('select')
				->table('user_activity_search_params')
				->fields('case_id')
				->where('user_activity_id', $this->model->id())
				->limit(1)
				->execute()->current();

			if ($row && $row->case_id) {

				if (in_array('case_id', $fields)) {
					$data['case_id'] = $row->case_id;
				}

				if (in_array('case_link', $fields)) {
					$caseRow = $this->pixie->db->query('select')
						->table('case')
						->fields('organization_id')
						->where('id', $row->case_id)
						->limit(1)
						->execute()->current();

					if ($caseRow && $caseRow->organization_id) {
						$data['case_link'] = '/cases/' . $caseRow->organization_id . '/cm/' . $row->case_id;
					}
				}
			}
		}

		return $data;
	}

	protected function formatActionDetails($name, $options, $model)
	{
		$actionViewer = $this->pixie->activityLogger->newActionViewer($model);
		$formattedDetails = $actionViewer->formatDetails();
		$details = [];
		if ($formattedDetails) {
			foreach ($formattedDetails as $label => $value) {
				$details[] = [
					'label' => $label,
					'value' => $value
				];
			}
		}

		return $details;
	}

	protected function formatActionChanges($name, $options, $model)
	{
		$actionViewer = $this->pixie->activityLogger->newActionViewer($model);
		$formattedChanges = $actionViewer->formatChanges();
		$changes = [];
		if ($formattedChanges) {
			foreach ($formattedChanges as $label => $value) {
				$changes[] = [
					'label' => $label,
					'value' => $value
				];
			}
		}

		return $changes;
	}

	protected function formatUserFullName($name, $options, $model)
	{
		return ($model->user && $model->user->loaded()) ? $model->user->getFullName() : '';
	}

	protected function formatUserOrgName($name, $options, $model)
	{
		if ($model->user && $model->user->loaded() && $model->user->organization && $model->user->organization->loaded()) {
			return $model->user->organization->name;
		}

		return '';
	}

	protected function formatUserOrgId($name, $options, $model)
	{
		if ($model->user && $model->user->loaded() && $model->user->organization && $model->user->organization->loaded()) {
			return (int) $model->user->organization->id();
		}

		return null;
	}

	protected function formatActionDate($name, $options, $model)
	{
		$actionDate = TimeFormat::fromDBDatetime($model->date);
		return TimeFormat::getDate($actionDate);
	}

	protected function formatActionTime($name, $options, $model)
	{
		$actionDate = TimeFormat::fromDBDatetime($model->date);
		return TimeFormat::getTimeWithSeconds($actionDate);
	}

	protected function formatActionTitle($name, $options, $model)
	{
		return $this->pixie->activityLogger->getFullActionTitle($model->action);
	}

	protected function formatCaseId($name, $options, $model)
	{

		return null;
	}

}