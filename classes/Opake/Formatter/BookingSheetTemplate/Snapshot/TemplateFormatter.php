<?php

namespace Opake\Formatter\BookingSheetTemplate\Snapshot;

use Opake\Formatter\BaseDataFormatter;
use Opake\Model\BookingSheetTemplate;

class TemplateFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'fields'
			],
			'fieldMethods' => [
				'id' => 'int',
				'fields' => 'templateFields'
			]
		]);
	}

	protected function formatTemplateFields($name, $options, $model)
	{

		$defaultConfig = BookingSheetTemplate::getDefaultFieldsConfig();

		$fields = $model->fields->find_all();
		$newConfig = [];
		foreach ($fields as $fieldModel) {
			$fieldId = $fieldModel->field;
			if (isset($defaultConfig[$fieldId])) {
				$defaultFieldConfig = $defaultConfig[$fieldId];
				$defaultFieldConfig['x'] = (int) $fieldModel->x;
				$defaultFieldConfig['y'] = (int) $fieldModel->y;
				$defaultFieldConfig['active'] = true;

				$newConfig[$fieldId] = $defaultFieldConfig;
			}
		}

		return $newConfig;
	}
}