<?php

namespace OpakeAdmin\Helper\Import;

class PrefCardStaff extends AbstractImport
{

	const HEADER_INFO_ROW_DATA = 2;
	const START_ROW_NOTES_DATA = 6;
	const COUNT_ROWS_NOTES_DATA = 10;
	const START_ROW_ITEMS_DATA = 18;

	/**
	 * Errors occurred by loading
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Template Name
	 * @var string
	 */
	protected $name;

	/**
	 * Procedures
	 * @var array
	 */
	protected $caseTypes = [];

	/**
	 * Inventory Items
	 * @var array
	 */
	protected $items = [];

	/**
	 * Notes
	 * @var array
	 */
	protected $notes = [];

	/**
	 * Errors
	 * @return array
	 */
	function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Template Name
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * Procedures
	 * @return array
	 */
	function getCaseTypes()
	{
		return $this->caseTypes;
	}

	/**
	 * Inventory Items
	 * @return array
	 */
	function getItems()
	{
		return $this->items;
	}

	/**
	 * Notes
	 * @return array
	 */
	function getNotes()
	{
		return $this->notes;
	}

	
	/**
	 * Load data from excel
	 * @param string $filename
	 * @param int $orgId
	 * @return boolean Success
	 */
	public function load($filename, $orgId)
	{
		$objPHPExcel = $this->readFromExcel($filename);

		$sheet = $objPHPExcel->getActiveSheet();

		$this->name = $sheet->getCell('A' . self::HEADER_INFO_ROW_DATA)->getValue();

		$caseTypesCodesString = (string) $sheet->getCell('b' . self::HEADER_INFO_ROW_DATA)->getValue();
		$caseTypesCodesString = str_replace('.', ',', $caseTypesCodesString);
		$caseTypesCodes = array_map('trim', explode(',', $caseTypesCodesString));
		$unknownTypes = [];

		foreach ($caseTypesCodes as $caseTypesCode) {
			if (!empty($caseTypesCode)) {
				$caseType = $this->pixie->orm->get('Cases_Type')
					->where('code', $caseTypesCode)
					->where('organization_id', $orgId)
					->find();
				if (!$caseType->loaded()) {
					$unknownTypes[] = $caseTypesCode;
				}
				$this->caseTypes[] = $caseType;
			}
		}

		if (!empty($unknownTypes)) {
			$this->errors[] = 'Unknown procedures: ' . implode(', ', $unknownTypes);
		}

		for ($i = self::START_ROW_NOTES_DATA; $i < (self::START_ROW_NOTES_DATA + self::COUNT_ROWS_NOTES_DATA); $i++) {
			$noteName = $sheet->getCell('A' . $i)->getValue();
			$noteText = $sheet->getCell('B' . $i)->getValue();
			if (!empty($noteName) && !empty($noteText)) {
				$note = $this->pixie->orm->get('PrefCard_Staff_Note');
				$note->name = $noteName;
				$note->text = $noteText;
				$this->notes[] = $note;
			}
		}

		$highestRow = $sheet->getHighestRow();
		for ($i = self::START_ROW_ITEMS_DATA; $i <= $highestRow; $i++) {
			$stageId = $sheet->getCell('A' . $i)->getValue();
			$itemNum = $sheet->getCell('B' . $i)->getValue();
			$quantity = $sheet->getCell('C' . $i)->getValue();
			if ($itemNum) {
				$item = $this->pixie->orm->get('PrefCard_Staff_Item');
				$item->item_number = $itemNum;
				$item->default_qty = $quantity;
				if ($stageId) {
					$item->stage_id = $stageId;
				}
				$inventory = $this->pixie->orm->get('Inventory')
					->where('item_number', $itemNum)
					->where('organization_id', $orgId)
					->find();
				if ($inventory->loaded()) {
					$item->inventory_id = $inventory->id();
				}
				$this->items[] = $item;
			}
		}

		if ($this->errors) {
			return false;
		}
		return true;
	}

}
