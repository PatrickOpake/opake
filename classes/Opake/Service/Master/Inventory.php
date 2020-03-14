<?php

namespace Opake\Service\Master;

use Opake\Helper\Currency;
use PHPExcel_IOFactory;

class Inventory extends \Opake\Service\AbstractService
{

	const START_ROW_DATA = 4;
	const END_COLUMN_DATA = 26;

	protected $base_model = 'Inventory';
	public static $allowed_file_formats = ['csv', 'xls', 'xlsx'];

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
		$this->orm->get('Inventory')->where('organization_id', $org_id)->delete_all();
		for ($row = self::START_ROW_DATA; $row <= $highestRow; $row++) {
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
			$model = $this->orm->get('Inventory');
			$dataArray = array_slice($rowData[0], 0, self::END_COLUMN_DATA);
			$data = $dataArray;

			$filtered_array = array_filter($dataArray);
			if (!empty($filtered_array)) {

				$model->organization_id = $org_id;
				if (isset($data[0])) {
					$model->item_number = (string)$data[0];
				}
				if (isset($data[1])) {
					$model->name = $data[1];
				}
				if (isset($data[2])) {
					$model->desc = $data[2];
				}
				if (isset($data[3])) {
					$model->type = $data[3];
				}
				if (isset($data[4])) {
					$model->is_implantable = $data[4] === 'Yes' ? 1 : 0;
				}
				if (isset($data[5])) {
					$model->is_reusable = $data[5] === 'Yes' ? 1 : 0;
				}
				if (isset($data[6])) {
					$model->is_remanufacturable = $data[6] === 'Yes' ? 1 : 0;
				}
				if (isset($data[7])) {
					$model->is_latex = $data[7] === 'Yes' ? 1 : 0;
				}
				if (isset($data[8])) {
					$model->is_hazardous = $data[8] === 'Yes' ? 1 : 0;
				}
				if (isset($data[9])) {
					$model->hims_indicator = $data[9];
				}
				if (isset($data[10])) {
					$model->hcpcs = $data[10];
				}
				if (isset($data[11])) {
					$model->qty_per_uom = $data[11];
				}
				if (isset($data[12])) {
					$uom = $this->pixie->orm->get('Inventory_UOM')
						->where('name', $data[12])
						->find();
					if ($uom->loaded()) {
						$model->uom_id = $uom->id();
					}
				}
				if (isset($data[13])) {
					$model->unit_price = Currency::parseString($data[13]);
				}
				if (isset($data[15])) {
					$model->charge_amount = Currency::parseString($data[15]);
				}
				if (isset($data[16])) {
					$model->status = $data[16];
				}
				if (isset($data[17])) {
					$model->unspsc = $data[17];
				}
				if (isset($data[18])) {
					$model->ndc = $data[18];
				}
				if (isset($data[20])) {
					$model->manufacturer_catalog = $data[20];
				}
				if (isset($data[21])) {
					$model->distributor_name = $data[21];
				}
				if (isset($data[22])) {
					$model->distributor_catalog = $data[22];
				}
				if (isset($data[23])) {
					$model->gln = $data[23];
				}
				if (isset($data[24])) {
					$model->gtin = $data[24];
				}
				if (isset($data[25])) {
					$model->barcode = $data[25];
				}
				if (isset($data[26])) {
					$model->barcode_type = $data[26];
				}
				if (isset($data[27])) {
					$model->image = $data[27];
				}
				if (isset($data[28])) {
					$model->shipping_type = $data[28];
				}
				if (isset($data[29])) {
					$model->unit_weight = $data[29];
				}
				if (isset($data[30])) {
					$model->min_level = $data[30];
				}
				if (isset($data[31])) {
					$model->max_level = $data[31];
				}
				if (isset($data[32])) {
					$model->total_units = $data[32];
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
					$this->saveSupply($model);
					if (isset($data[14])) {
						$this->saveMultiplier($model, $data[14]);
					}
					if (isset($data[19])) {
						$this->saveManufacturer($model, Currency::parseString($data[19]));
					}

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

	public function saveSupply($model)
	{
		if ($model->distributor_name && $model->distributor_catalog) {
			$distributor = $this->pixie->orm->get('Vendor')->where([
				['name', $model->distributor_name],
				['organization_id', $model->organization_id]

			])->find();
			if ($distributor->loaded()) {
				if (!$distributor->is_dist) {
					$distributor->is_dist = 1;
					$distributor->save();
				}
			} else {
				$distributor = $this->pixie->orm->get('Vendor');
				$distributor->organization_id = $model->organization_id;
				$distributor->name = $model->distributor_name;
				$distributor->is_dist = 1;
				$distributor->save();
			}

			$supply = $this->pixie->orm->get('Inventory_Supply')->where([
				['inventory_id', $model->id],
				['vendor_id', $distributor->id],
				['device_id', $model->distributor_catalog],
			])->find();

			$supply->inventory_id = $model->id;
			$supply->vendor_id = $distributor->id;
			$supply->device_id = $model->distributor_catalog;
			$supply->save();
		}
	}

	public function saveManufacturer($model, $manufacturer_name)
	{
		if ($manufacturer_name) {
			$manufacturer = $this->pixie->orm->get('Vendor')->where([
				['name', $manufacturer_name],
				['organization_id', $model->organization_id]
			])->find();

			if ($manufacturer->loaded()) {
				$model->manf_id = $manufacturer->id;
				if (!$manufacturer->is_manf) {
					$manufacturer->is_manf = 1;
					$manufacturer->save();
				}
			} else {
				$vendor = $this->pixie->orm->get('Vendor');
				$vendor->organization_id = $model->organization_id;
				$vendor->name = $manufacturer_name;
				$vendor->is_manf = 1;
				$vendor->save();
				$model->manf_id = $vendor->id;
			}

			$model->save();
		}
	}

	public function saveMultiplier($inventoryModel, $multiplier_cost)
	{
		$model = $this->orm->get('Inventory_Multiplier');
		if ($multiplier_cost) {
			$model->inventory_id = $inventoryModel->id();
			$model->organization_id = $inventoryModel->organization_id;
			$model->multiplier = $multiplier_cost;
			$model->save();
		}
	}
}
