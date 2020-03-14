<?php

namespace Opake\Formatter\Inventory\Invoice;

use Opake\Formatter\BaseDataFormatter;

class InventoryInvoiceFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
				'fields' => [
					'id',
					'uploaded_file_id',
					'name',
					'date',
					'manufacturers',
					'page_count',
					'items'
				],
				'fieldMethods' => [
					'id' => 'int',
					'uploaded_file_id' => 'int',
					'manufacturers' => 'manufacturers',
					'page_count' => 'pageCount',
					'items' => 'items'
				]
		]);
	}

	protected function formatManufacturers($name, $options, $model)
	{
		$manufacturers = [];
		foreach ($model->manufacturers->find_all() as $manufacturer) {
			$manufacturers[] = $manufacturer->toShortArray();
		}
		return $manufacturers;
	}

	protected function formatPageCount($name, $options, $model)
	{
		if ($model->file->isPDF()) {
			$pdf = new \FPDI();
			return $pdf->setSourceFile($model->file->getSystemPath());
		}
		return null;
	}

	protected function formatItems($name, $options, $model)
	{
		$fields = [];
		foreach ($model->items->find_all() as $field) {
			$fields[] = $field->toShortArray();
		}
		return $fields;
	}

}
