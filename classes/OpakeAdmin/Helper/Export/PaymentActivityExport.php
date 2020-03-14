<?php

namespace OpakeAdmin\Helper\Export;

use Opake\Model\AbstractModel;
use PHPExcel_Style_Alignment;

class PaymentActivityExport
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
	 * @throws PHPExcel_Exception
	 */
	public function exportToExcel()
	{
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 600);

		$excel = new \PHPExcel();
		$excel->getProperties()
			->setCreator('Opake')
			->setLastModifiedBy('Opake')
			->setTitle('Payment Activity')
			->setSubject('Payment Activity');

		$sheet = $excel->getSheet(0);
		$sheet->setTitle('Payment Activity');

		foreach($excel->getActiveSheet()->getRowDimensions() as $rd) {
			$rd->setRowHeight(-1);
		}


		$columnsMapping = $this->getColumnsMapping();
		$columnsNames = array_values($columnsMapping);
		$columnsKeys = array_keys($columnsMapping);

		foreach ($columnsNames as $index => $name) {
			$sheet->setCellValueByColumnAndRow($index, 1, $name);
		}

		$rowNum = 2;
		foreach ($this->models as $model) {
			$rowData = $model->getFormatter('PaymentActivityListEntry')->toArray();

			foreach ($columnsKeys as $index => $key) {
				$value = $this->formatValue($key, $rowData[$key]);
				$sheet->setCellValueByColumnAndRow($index, $rowNum, $value);
				$sheet->getStyleByColumnAndRow($index, $rowNum)->getAlignment()
					->setWrapText(true)
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			}
			$sheet->getRowDimension($rowNum)->setRowHeight(-1);

			$rowNum++;
		}

		$highestColumn = $sheet->getHighestColumn();
		for ($col = ord('a'); $col <= ord(strtolower($highestColumn)); $col++) {
			$sheet->getColumnDimension(chr($col))->setAutoSize(true);
		}


		$writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$tmpPath = tempnam(sys_get_temp_dir(), 'opk');
		$writer->save($tmpPath);

		if (is_file($tmpPath)) {
			/** @var \Opake\Model\UploadedFile $uploadedFile */
			$uploadedFile = $this->pixie->orm->get('UploadedFile');
			$uploadedFile->storeContent($this->getFileName(), file_get_contents($tmpPath), [
				'is_protected' => true,
				'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			]);
			$uploadedFile->save();

			unlink($tmpPath);

			return $uploadedFile;
		}

		return null;
	}


	protected function getColumnsMapping()
	{
		return [
			'date_of_payment' => 'Date of Payment',
			'patient_last_name' => 'Patient Last Name',
			'patient_first_name' => 'Patient First Name',
			'payment_source' => 'Payment Source',
			'payment_method' => 'Payment Method',
			'amount' => 'Payment Amount',
		];
	}

	protected function getColumnsFormat($columnKey)
	{
		$formats = [
			'amount' => function($value) {
				return '$' . number_format((float) $value, 2, '.', ',');
			},
		];

		return $formats[$columnKey] ?? null;
	}

	protected function formatValue($key, $value)
	{
		if ($format = $this->getColumnsFormat($key)) {
			return call_user_func($format, $value);
		}
		return $value;
	}

	protected function getFileName()
	{
		return 'Payment_Activity_export.xlsx';
	}
}