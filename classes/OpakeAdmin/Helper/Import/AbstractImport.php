<?php

namespace OpakeAdmin\Helper\Import;

/**
 * Importa data
 */
use PHPExcel_IOFactory;

class AbstractImport
{

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	/**
	 * Read excel file
	 * @param string $filename
	 * @throws \Exception
	 * @return \PHPExcel
	 */
	protected function readFromExcel($filename)
	{
		$inputFileType = \Opake\Helper\PHPExcel\IOFactory::identify($filename);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);

		try {
			$objPHPExcel = $objReader->load($filename);
		} catch (\Exception $e) {
			throw new \Exception("Invalid format of the loaded document. You can upload file in the following formats: XLSX, XLS, CSV");
		}
		return $objPHPExcel;
	}

}
