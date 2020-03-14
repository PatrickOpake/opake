<?php

namespace Opake\Formatter\Efax\InboundFax;

use Opake\Formatter\BaseDataFormatter;

class WidgetListFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
			    'from_fax',
			    'received_date',
			    'is_read',
			    'site_name'
			],
			'fieldMethods' => [
				'from_fax' => 'fromFax',
				'received_date' => 'toDateTime',
			    'is_read' => 'isRead',
			    'site_name' => 'siteName'
			]
		]);
	}

	protected function formatFromFax($name, $options, $model)
	{
		if ($model->from_fax) {
			$faxNumber = $model->from_fax;
			$matches = [];
			if (preg_match('/^(.{1})(.{3})(.{3})(.+)$/', $faxNumber, $matches)) {
				return '+' . $matches[1] . '-' . $matches[2] . '-' . $matches[3] . '-' . $matches[4];
			}
		}

		return $model->from_fax;
	}

	protected function formatIsRead($name, $options, $model)
	{
		$app = \Opake\Application::get();
		$user = $app->auth->user();

		return $model->isReadByUser($user);
	}

	protected function formatSiteName($name, $options, $model)
	{
		$site = $model->site;
		if ($site->loaded()) {
			return $site->name;
		}

		return '';
	}
}