<?php

namespace Opake\Service\Master;

use Opake\Helper\Currency;
use Opake\Helper\Minify\Exception\Exception;
use PHPExcel_IOFactory;
use Opake\Helper\Config;
use PHPixie\Validate\Validator;

class Charges extends \Opake\Service\AbstractService
{

	const START_ROW_DATA = 4;

	protected $base_model = 'Master_Charge';
	public static $allowed_file_formats = ['csv', 'xls', 'xlsx'];

	public function getChargeByCPT($cpt, $org_id)
	{
		return $this->orm->get($this->base_model)->where([['organization_id', $org_id], ['cpt', $cpt]])->find();
	}

	public function upload($org_id, $fname)
	{
		self::checkExtension($fname);
		$inputFileType = PHPExcel_IOFactory::identify($fname);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		if ($inputFileType == 'CSV') {
			$objReader->setDelimiter(';');
		}

		try {
			$objPHPExcel = $objReader->load($fname);
		} catch (\Exception $e) {
			throw new \Exception("Invalid format of the loaded document. You can upload file in the following formats: XLSX, XLS, CSV");
		}
		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		$this->beginTransaction();
		$this->orm->get('Master_Charge')->where('organization_id', $org_id)->delete_all();
		for ($row = self::START_ROW_DATA; $row <= $highestRow; $row++) {
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
			$model = $this->orm->get('Master_Charge');

			$data = $rowData[0];
			$filtered_array = array_filter($rowData[0]);
			if (!empty($filtered_array)) {
				$model->organization_id = $org_id;
				if (isset($data[0])) {
					$model->cdm = $data[0];
				}
				if (isset($data[1])) {
					$model->desc = $data[1];
				}
				if (isset($data[2])) {
					$model->amount = Currency::parseString($data[2]);
				}
				if (isset($data[3])) {
					$model->revenue_code = $data[3];
				}
				if (isset($data[4])) {
					$model->department = $data[4];
				}
				if (isset($data[5])) {
					$model->cpt = $data[5];
				}
				if (isset($data[6])) {
					$model->cpt_modifier1 = $data[6];
				}
				if (isset($data[7])) {
					$model->cpt_modifier2 = $data[7];
				}
				if (isset($data[8])) {
					$model->unit_price = Currency::parseString($data[8]);
				}
				if (isset($data[9])) {
					$model->ndc = $data[9];
				}
				if (isset($data[10])) {
					$model->active = $data[10];
				}
				if (isset($data[11])) {
					$model->general_ledger = $data[11];
				}

				$validator = $model->getValidator();
				if (!$validator->valid()) {
					$this->rollback();
					$errors_text = '';
					foreach ($validator->errors() as $errors) {
						$errors_text .= implode('; ', $errors) . ";<br/>";
					}
					throw new \Exception(trim($errors_text, '; '));
				}

				try {
					$model->save();
				} catch (\Exception $e) {
					$this->rollback();
					throw new \Exception($e->getMessage());
				}
			}
		}
		$this->commit();
	}

	public static function checkExtension($filename)
	{
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		if (!in_array($ext, self::$allowed_file_formats)) {
			throw new \Exception("Invalid format of the loaded document. You can upload file in the following formats: XLSX, XLS, CSV");
		}
	}

}
