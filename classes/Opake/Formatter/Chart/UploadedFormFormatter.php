<?php

namespace Opake\Formatter\Chart;

use Opake\Formatter\BaseDataFormatter;

class UploadedFormFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'name',
				'include_header',
				'is_pdf',
				'page_count',
				'dynamic_fields'
			],
			'fieldMethods' => [
				'id' => 'int',
				'include_header' => 'bool',
				'is_pdf' => 'isPDF',
				'page_count' => 'pageCount',
				'dynamic_fields' => 'dynamicFields'
			]
		]);
	}

	protected function formatIsPDF($name, $options, $model)
	{
		return $model->file->isPDF();
	}

	protected function formatPageCount($name, $options, $model)
	{
		if ($model->file->isPDF()) {
			$pdf = new \FPDI();
			return $pdf->setSourceFile($model->file->getSystemPath());
		}
		return null;
	}

	protected function formatDynamicFields($name, $options, $model)
	{
		$fields = [];
		foreach ($model->dynamic_fields->find_all() as $field) {
			$fields[] = $field->toArray();
		}
		return $fields;
	}

}
