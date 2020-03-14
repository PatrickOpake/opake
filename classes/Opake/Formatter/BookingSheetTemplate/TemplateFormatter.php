<?php

namespace Opake\Formatter\BookingSheetTemplate;

use Opake\Formatter\BaseDataFormatter;
use Opake\Model\BookingSheetTemplate;

class TemplateFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
			    'name',
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
		$fields = $this->getFields($model);
		if (!empty($this->config['onlyActive'])) {
			$newFields = [];
			foreach ($fields as $fieldId => $fieldData) {
				if (!empty($fieldData['active'])) {
					$newFields[$fieldId] = $fieldData;
				}
			}

			return $newFields;
		}

		return $fields;
	}

	protected function getFields($model)
	{
		$defaultConfig = BookingSheetTemplate::getDefaultFieldsConfig();
		if ($model->type == BookingSheetTemplate::TYPE_DEFAULT && (!$model->loaded())) {
			return $defaultConfig;
		}

		$fields = $model->fields->find_all();
		$newConfig = [];
		foreach ($fields as $fieldModel) {
			$fieldId = $fieldModel->field;
			if (isset($defaultConfig[$fieldId])) {
				$defaultFieldConfig = $defaultConfig[$fieldId];
				$defaultFieldConfig['x'] = (int) $fieldModel->x;
				$defaultFieldConfig['y'] = (int) $fieldModel->y;
				$defaultFieldConfig['active'] = (bool) $fieldModel->active;

				$newConfig[$fieldId] = $defaultFieldConfig;
			}
		}

		foreach ($defaultConfig as $fieldId => $defaultFieldConfig) {
			if (!isset($newConfig[$fieldId])) {
				$defaultFieldConfig['x'] = 0;
				$defaultFieldConfig['y'] = 0;
				$defaultFieldConfig['active'] = false;

				$newConfig[$fieldId] = $defaultFieldConfig;
			}
		}

		return $newConfig;
	}
}