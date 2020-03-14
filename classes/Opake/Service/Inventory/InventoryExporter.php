<?php

namespace Opake\Service\Inventory;

use Opake\Model\AbstractModel;
use Opake\Service\Master\Inventory;
use PHPExcel_Exception;
use PHPExcel_IOFactory;
use PHPExcel_Style_Protection;

class InventoryExporter
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
	 * @var string
	 */
	protected $output = '';

	/**
	 * @var string
	 */
	protected $template = '';

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	/**
	 * @return string
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * @param string $path
	 */
	public function setTemplate($path)
	{
		$this->template = $path;
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
	 * @throws PHPExcel_Exception
	 */
	public function exportToExcel()
	{
		$inputFileType = PHPExcel_IOFactory::identify($this->getTemplate());
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);

		try {
			$excel = $objReader->load($this->getTemplate());
		} catch (\Exception $e) {
			throw new \Exception("Invalid format of the loaded document. You can upload file in the following formats: XLSX, XLS, CSV");
		}

		$excel->getProperties()
			->setCreator('Opake')
			->setLastModifiedBy('Opake')
			->setTitle('Item Master')
			->setSubject('Item Master');

		$sheet = $excel->getSheet(0);
		$sheet->setTitle('Item Master');

		$rowNum = Inventory::START_ROW_DATA;
		foreach ($this->models as $model) {
			$formattedRow = $this->formatRow($model);
			foreach ($formattedRow as $index => $value) {
				$sheet->setCellValueByColumnAndRow($index, $rowNum, $value);
			}
			$rowNum++;
		}

		$excel->getActiveSheet()->getStyle('A4:X'.$rowNum)
			->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

		$writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$tmpPath = tempnam(sys_get_temp_dir(), 'opk');
		$writer->save($tmpPath);

		if (is_file($tmpPath)) {
			$this->output = file_get_contents($tmpPath);
			unlink($tmpPath);
		}
	}

	/**
	 * @param \Opake\Model\Inventory $model
	 * @return array
	 */
	protected function formatRow($model)
	{
		$data = [
			$model->item_number,
			$model->name,
			$model->desc,
			$model->type,
			$model->is_implantable ? 'Yes' : 'No',
			$model->is_reusable ? 'Yes' : 'No',
			$model->is_remanufacturable ? 'Yes' : 'No',
			$model->is_latex ? 'Yes' : 'No',
			$model->is_hazardous ? 'Yes' : 'No',
			$model->hims_indicator,
			$model->hcpcs,
			$model->qty_per_uom,
			$model->uom->name,
			$model->unit_price,
			$model->getCostMultiplier() ? number_format($model->getCostMultiplier(), 2, '.', '') : '',
			$model->charge_amount,
			$model->status,
			$model->unspsc,
			$model->ndc,
			$model->manufacturer->name,
			$model->manufacturer_catalog,
			$model->distributor_name,
			$model->distributor_catalog,
			$model->gln,
			$model->gtin,
			$model->barcode,
			$model->barcode_type,
			'',
			$model->shipping_type,
			$model->unit_weight,
			$model->min_level,
			$model->max_level,
			$model->total_units,
		];
		return $data;
	}
}