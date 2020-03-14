<?php

namespace OpakeAdmin\Helper\Export;

use Opake\Helper\TimeFormat;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_NumberFormat;

class ProceduresExport
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var string
	 */
	protected $delimiter = ',';

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	public function generateCsv($procedures)
	{
		ini_set('memory_limit', '1024M');
		$tmpPath = $this->pixie->app_dir . '/_tmp/' . uniqid();
		$fh = fopen($tmpPath, 'w+');
		$columnLabels = $this->getColumnLabels();
		$firstRow = [];

		foreach ($columnLabels as $columnName) {
			$firstRow[] = (isset($columnLabels[$columnName])) ? $columnLabels[$columnName] : $columnName;
		}
		fputcsv($fh, $firstRow, $this->delimiter);

		foreach ($procedures as $procedure) {
			try {
				if ($procedure->loaded()) {

					$row = [];
					$row[] = $procedure->name;
					$row[] = $procedure->cpt_id ? $procedure->cpt->code : $procedure->code;
					$row[] = $procedure->length ? date('H', strtotime($procedure->length)) : '';
					$row[] = $procedure->length ? date('i', strtotime($procedure->length)) : '';
					$row[] = $procedure->getStatusStr();

					fputcsv($fh, $row, $this->delimiter);
				}
			} catch (\Exception $e) {
				$this->pixie->logger->exception($e);
			}
		}

		fclose($fh);

		/** @var \Opake\Model\UploadedFile $uploadedFile */
		$uploadedFile = $this->pixie->orm->get('UploadedFile');
		$uploadedFile->storeContent($this->getFileName(), file_get_contents($tmpPath), [
			'is_protected' => true,
			'mime_type' => 'text/csv'
		]);
		$uploadedFile->save();

		unlink($tmpPath);

		return $uploadedFile;
	}

	public function generateExcel($procedures)
	{
		$excel = new \PHPExcel();
		$excel->getProperties()
			->setCreator('Opake')
			->setLastModifiedBy('Opake')
			->setTitle('Procedures')
			->setSubject('Procedures');

		$sheet = $excel->getSheet(0);
		$sheet->setTitle('Procedures');

		for ($col = ord('a'); $col <= ord('e'); $col++) {
			$sheet->getColumnDimension(chr($col))->setAutoSize(true);
		}

		$columnLabels = $this->getColumnLabels();
		foreach ($columnLabels as $index => $columnName) {
			$sheet->setCellValueByColumnAndRow($index, 1, $columnName);
		}

		$excel->getDefaultStyle()
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);

		$rowNum = 2;
		foreach ($procedures as $procedure) {
			try {
				if ($procedure->loaded()) {
					$sheet->setCellValueByColumnAndRow(0, $rowNum, $procedure->name);
					$sheet->setCellValueByColumnAndRow(1, $rowNum, $procedure->cpt_id ? $procedure->cpt->code : $procedure->code);
					$sheet->setCellValueByColumnAndRow(2, $rowNum, $procedure->length ? (int)date('H', strtotime($procedure->length)) : null);
					$sheet->setCellValueByColumnAndRow(3, $rowNum, $procedure->length ? (int)date('i', strtotime($procedure->length)) : null);
					$sheet->setCellValueByColumnAndRow(4, $rowNum, $procedure->getStatusStr());
					$rowNum++;
				}
			} catch (\Exception $e) {
				$this->pixie->logger->exception($e);
			}
		}

		$writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		ob_start();
		$writer->save('php://output');
		$content = ob_get_clean();
		return $content;
	}

	protected function getFileName()
	{
		return 'Procedures_export.xlsx';
	}

	/**
	 * @return array
	 */
	protected function getColumnLabels()
	{
		return [
			'Description',
			'HCPCS/CPT Code',
			'Case Length (hr)',
			'Case Length (min)',
			'Status (Active/Inactive)'
		];
	}
}