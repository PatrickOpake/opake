<?php

namespace OpakeAdmin\Helper\Export;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Style_Protection;

class InsurancesDatabaseExport
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
	 * @throws \PHPExcel_Exception
	 */
	public function exportToExcel()
	{
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 600);

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
			->setTitle('Insurances Payor')
			->setSubject(static::getCurrentExportSubject());

		$sheet = $excel->getSheet(0);
		$sheet->setTitle('Insurances');

		$rowNum = 4;
		foreach ($this->models as $model) {
			$formattedRow = $this->formatRow($model);
			foreach ($formattedRow as $index => $value) {
				$sheet->setCellValueByColumnAndRow($index, $rowNum, $value);
			}
			$rowNum++;
		}

		$excel->getActiveSheet()->getStyle('A4:CE'.$rowNum)
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
			$model->name,
			$model->ub04_payer_id,
			$model->cms1500_payer_id,
			$model->navicure_eligibility_payor_id,
			$model->carrier_code,
			$model->insurance_type,
			TimeFormat::getDateTime($model->last_change_date),
			$model->last_change_user->loaded() ? $model->last_change_user->getFullName() : '',
		];

		foreach ($model->addresses->order_by('id')->find_all() as $address) {
			$data[] = $address->address;
			$data[] = $this->formatCity($address);
			$data[] = $this->formatState($address);
			$data[] = $address->zip_code;
			$data[] = $address->phone;
		}

		return $data;
	}

	protected function formatCity($model)
	{
		if ($model->city->loaded()) {
			return $model->city->name;
		}

		return '';
	}

	protected function formatState($model)
	{
		if ($model->state->loaded()) {
			return $model->state->code;
		}

		return '';
	}

	public static function getCurrentExportSubject()
	{
		return 'Insurances Payor (v2)';
	}
}