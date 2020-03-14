<?php

namespace Opake\Service\Master\Charges;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;
use Opake\Service\Master\Charges;
use Opake\Service\Master\Inventory;
use PHPExcel_Exception;
use PHPExcel_IOFactory;
use PHPExcel_Style_Protection;

class ChargeMasterExporter
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
			->setTitle('Charge Master')
			->setSubject('Charge Master');

		$sheet = $excel->getSheet(0);
		$sheet->setTitle('Charge Master');

		$rowNum = Charges::START_ROW_DATA;
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

	public function getMimeType()
	{
		return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	}

	public function getFileName()
	{
		return 'Charge_Master_' . (new \DateTime())->format('Y-m-d_h-i-s') . '.xlsx';
	}

	/**
	 * @param \Opake\Model\Inventory $model
	 * @return array
	 */
	protected function formatRow($model)
	{
		$data = [
			$model->cdm,
			$model->desc,
			$model->amount,
			$model->revenue_code,
			$model->department,
			$model->cpt,
			$model->cpt_modifier1,
			$model->cpt_modifier2,
			$model->unit_price,
			$model->ndc,
			$model->active,
			$model->general_ledger,
			$model->notes,
			TimeFormat::getDate($model->last_edited_date),
			$model->historical_price,
		];
		return $data;
	}
}