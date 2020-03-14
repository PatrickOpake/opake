<?php

namespace OpakeAdmin\Helper\Export;

use Opake\Model\AbstractModel;
use Opake\Helper\Config;
use PHPExcel_IOFactory;
use PHPExcel_Exception;

class InventoryExport
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var AbstractModel[]
	 */
	protected $models;

	/**
	 * @var array
	 */
	protected $filterValues;

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	/**
	 * @return \Opake\Model\AbstractModel[]
	 */
	public function getModels()
	{
		return $this->models;
	}

	/**
	 * @param \Opake\Model\AbstractModel[] $models
	 */
	public function setModels($models)
	{
		$this->models = $models;
	}

	/**
	 * @param array $values
	 */
	public function setFilterValues($values)
	{
		$this->filterValues = $values;
	}

	/**
	 * @throws PHPExcel_Exception
	 */
	public function exportToExcel()
	{
		$template = $this->pixie->root_dir . Config::get('app.templates.inventory_report');
		$inputFileType = PHPExcel_IOFactory::identify($template);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($template);

		$objPHPExcel->getProperties()
			->setCreator('Opake')
			->setLastModifiedBy('Opake')
			->setTitle('Inventory Report')
			->setSubject('Inventory Report');

		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		$sheet->setTitle('Inventory Report');

		$this->fillFilterParams($sheet);

		for ($col = ord('a'); $col <= ord('h'); $col++) {
			$sheet->getColumnDimension(chr($col))->setAutoSize(true);
		}

		$rowNum = 7;
		
		foreach ($this->models as $model) {
			$formattedRow = $this->formatRow($model);
			foreach ($formattedRow as $index => $value) {
				$sheet->setCellValueByColumnAndRow($index, $rowNum, $value);
			}
			$rowNum++;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
		ob_start();
		$objWriter->save('php://output');
		$content = ob_get_clean();
		return $content;
	}

	/**
	 * @param \Opake\Model\Inventory $model
	 * @return array
	 */
	protected function formatRow($model)
	{
		$inventory = $model->getFormatter('InventoryReportFormatter')->toArray();
		$data = [
			$inventory['item_number'],
			$inventory['name'],
			$inventory['desc'],
			$inventory['default_qty'],
			$inventory['actual_use'],
			$inventory['unit_price'],
			$inventory['total_cost'],
		];
		return $data;
	}

	/**
	 * @param PHPExcel_Worksheet $sheet
	 */
	protected function fillFilterParams($sheet)
	{
		if ($this->filterValues) {
			if (!empty($this->filterValues['start'])) {
				$sheet->setCellValue('B1', $this->filterValues['start']);
			}
			if (!empty($this->filterValues['end'])) {
				$sheet->setCellValue('D1', $this->filterValues['end']);
			}
			if (!empty($this->filterValues['inventory_type'])) {
				$sheet->setCellValue('B2', $this->filterValues['inventory_type']);
			}
			if (!empty($this->filterValues['inventory_manf'])) {
				$sheet->setCellValue('D2', $this->filterValues['inventory_manf']);
			}
			if (!empty($this->filterValues['inventory_desc'])) {
				$sheet->setCellValue('B3', $this->filterValues['inventory_desc']);
			}
			if (!empty($this->filterValues['doctor'])) {
				$sheet->setCellValue('D3', $this->filterValues['doctor']);
			}
			if (!empty($this->filterValues['procedure'])) {
				$sheet->setCellValue('B4', $this->filterValues['procedure']);
			}
			if (!empty($this->filterValues['inventory'])) {
				$sheet->setCellValue('D4', $this->filterValues['inventory']);
			}
		}
	}

}