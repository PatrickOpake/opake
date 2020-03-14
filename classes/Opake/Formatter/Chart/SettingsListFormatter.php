<?php

namespace Opake\Formatter\Chart;

use Opake\Formatter\BaseDataFormatter;

class SettingsListFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
			    'organization_id',
			    'uploaded_file_id',
			    'segment',
			    'type',
			    'name',
			    'include_header',
			    'is_landscape',
			    'doc_type_id',
			    'is_all_sites',
			    'is_all_case_types',
			    'url',
			    'sites',
			    'chart_group_ids',
			    'filename_for_export'
			],

		    'fieldMethods' => [
				'id' => 'int',
		        'organization_id' => 'int',
		        'uploaded_file_id' => 'int',
		        'include_header' => 'bool',
		        'is_landscape' => 'bool',
		        'doc_type_id' => 'int',
		        'is_all_sites' => 'bool',
		        'is_all_case_types' => 'bool',
		        'url' => 'fileUrl',
		        'sites' => 'sites',
		        'chart_group_ids' => 'chartGroupIds',
		        'filename_for_export' => ['modelMethod', [
			        'modelMethod' => 'getFileNameForExport'
		        ]]
		    ]
		]);
	}

	protected function formatSites($name, $options, $model)
	{
		$sites = [];
		if ($model->is_all_sites) {
			$sitesQuery = $this->pixie->orm->get('Site')->where('organization_id', $model->organization_id);
		} else {
			$sitesQuery = $model->sites;
		}
		foreach ($sitesQuery->find_all() as $site) {
			$sites[] = [
				'id' => $site->id,
				'name' => $site->name
			];
		}

		return $sites;
	}

	protected function formatFileUrl($name, $options, $model)
	{
		if ($model->file->loaded()) {
			return $model->file->getWebPath();
		}
		return null;
	}

	protected function formatChartGroupIds($name, $options, $model)
	{
		$ids = [];

		foreach ($model->getChartGroups() as $chartGroup) {
			$ids[] = (int) $chartGroup->id();
		}

		return $ids;

	}

}