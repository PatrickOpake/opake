<?php

namespace OpakeAdmin\Helper\Export;

use Opake\Helper\Config;
use PHPExcel_IOFactory;

class PrefCardStaff
{

	const HEADER_INFO_ROW_DATA = 2;
	const START_ROW_NOTES_DATA = 6;
	const COUNT_ROWS_NOTES_DATA = 10;
	const START_ROW_ITEMS_DATA = 18;

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
	 * Generate data from excel
	 * @param \Opake\Model\PrefCard\Staff $card
	 */
	public function generate($card)
	{
		$template = $this->pixie->root_dir . Config::get('app.templates.pref_card');
		$inputFileType = PHPExcel_IOFactory::identify($template);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($template);

		$caseTypesCodesString = '';
		foreach ($card->case_types->find_all() as $caseType) {
			$caseTypesCodesString .= $caseType->code . ',';
		}
		$caseTypesCodesString = trim($caseTypesCodesString, ',');

		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A' . self::HEADER_INFO_ROW_DATA, $card->name)
			->setCellValue('B' . self::HEADER_INFO_ROW_DATA, $caseTypesCodesString);

		$notes = $card->notes->find_all()->as_array();
		$additionalRows = 0;
		if (count($notes) > self::COUNT_ROWS_NOTES_DATA) {
			$additionalRows = count($notes) - self::COUNT_ROWS_NOTES_DATA;
			$objPHPExcel->getActiveSheet()->insertNewRowBefore(self::START_ROW_NOTES_DATA+1, $additionalRows);
		}
		$i = self::START_ROW_NOTES_DATA;
		foreach ($card->notes->find_all() as $note) {
			$objPHPExcel->getActiveSheet()
				->setCellValue('A' . $i, $note->name)
				->setCellValue('B' . $i, $note->text);
			$i++;
		}

		$i = self::START_ROW_ITEMS_DATA + $additionalRows;
		foreach ($card->items->find_all() as $item) {
			$objPHPExcel->getActiveSheet()
				->setCellValue('A' . $i, $item->stage_id)
				->setCellValue('B' . $i, $item->inventory->item_number)
				->setCellValue('C' . $i, $item->default_qty);
			$i++;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
		ob_start();
		$objWriter->save('php://output');
		$content = ob_get_clean();
		return $content;
	}

}
