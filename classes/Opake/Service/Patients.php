<?php

namespace Opake\Service;

use Opake\Helper\Currency;
use Opake\Model\Cases\Registration;
use PHPExcel_IOFactory;

class Patients extends AbstractService
{
	const START_ROW_DATA = 2;

	protected $base_model = 'Patient';
	public static $allowed_file_formats = ['csv', 'xls', 'xlsx'];

	public function updateExistedRegistrations($model)
	{
		//break the link
		/*$registrations = $this->orm->get('Cases_Registration')->where([
			['patient_id', $model->id],
			['status', Registration::STATUS_BEGIN],
		]);
		foreach ($registrations->find_all() as $registration) {
			$registration->fromPatient($model);
			$registration->save();

			$registrationInsurances = $registration->insurances->find_all();
			$patientInsurances = $model->insurances->find_all();
			foreach ($registrationInsurances as $regInsurances) {
				foreach ($patientInsurances as $patInsurance) {
					if ($patInsurance->id() == $regInsurances->selected_insurance_id) {

					}
				}
			}
		}*/
	}

	public function validate($modelName, $insuranceModelName, $data)
	{
		$errors = [];
		$model = $this->orm->get($modelName, isset($data->id) && $data->id ? $data->id : null);
		if ($data) {
			$model->fill($data);
		}

		$validator = $model->getValidator();
		if (!$validator->valid()) {
			$errors[strtolower($modelName)] = $validator->errors();
			$errors['length'] = count($validator->errors());
		}

		foreach ($data->insurances as $key => $insurance_data) {
			$insurance = $this->orm->get('Patient_Insurance');
			$insurance->fill($insurance_data);
			$validator = $insurance->getValidator();
			if (!$validator->valid()) {
				$errors[strtolower($insuranceModelName)][++$key] = $validator->errors();
				$errors['length'] = count($validator->errors());
			}
		}
		return $errors;
	}

	public function uploadFromExcel($org_id, $fname)
	{
		self::checkExtension($fname);
		$inputFileType = PHPExcel_IOFactory::identify($fname);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);

		try {
			$objPHPExcel = $objReader->load($fname);
		} catch (\Exception $e) {
			throw new \Exception("Invalid format of the loaded document. You can upload file in the following formats: XLSX, XLS, CSV");
		}
		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		$this->beginTransaction();
		for ($row = self::START_ROW_DATA; $row <= $highestRow; $row++) {
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
			$model = $this->orm->get('Patient');
			$stateModel = $this->pixie->orm->get('Geo_State');
			$cityModel = $this->pixie->orm->get('Geo_City');

			$data = $rowData[0];
			$filtered_array = array_filter($rowData[0]);
			if (!empty($filtered_array)) {
				$model->organization_id = $org_id;
				if (isset($data[0])) {
					$model->first_name = trim($data[0]);
				}
				if (isset($data[1])) {
					$model->last_name = trim($data[1]);
				}
				if (isset($data[2])) {
					$model->home_address = $data[2];
				}
				if (isset($data[3])) {
					$city = $cityModel->where('name', trim($data[3]))->find();
					if ($city->id) {
						$model->home_city_id = $city->id;
					} else {
						$this->rollback();
						throw new \Exception('Incorrect City');
					}
				}
				if (isset($data[4])) {
					$state = $stateModel->where('code', trim($data[4]))->find();
					if ($state->id) {
						$model->home_country_id = 235;
						$model->home_state_id = $state->id;
					} else {
						$this->rollback();
						throw new \Exception('Incorrect State');
					}
				}
				if (isset($data[5])) {
					if (is_numeric($data[5])) {
						$model->home_zip_code = trim($data[5]);
					} else {
						$this->rollback();
						throw new \Exception('Incorrect Zip code');
					}
				}
				if (isset($data[6])) {
					$phone = str_replace('-', '', trim($data[6]));
					if (is_numeric($phone) && (strlen($phone) == 10)) {
						$model->home_phone = $phone;
					} else {
						$this->rollback();
						throw new \Exception('Incorrect Phone');
					}
				}
				if (isset($data[7])) {
					try {
						$dob = \Opake\Helper\TimeFormat::formatToDB($data[7]);
						$model->dob = $dob;
					} catch (\Exception $e) {
						$this->rollback();
						throw new \Exception('Incorrect Date');
					}
				}
				if (isset($data[8])) {
					if (is_numeric(trim($data[8]))) {
						$model->mrn = $data[8];
					} else {
						$this->rollback();
						throw new \Exception('Incorrect MRN');
					}

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

	public function deleteWithCases($patient)
	{
		$this->beginTransaction();
		try {
			$registrations = $this->orm->get('Cases_Registration')->where([
				['patient_id', $patient->id]
			]);
			foreach ($registrations->find_all() as $reg) {
				$this->orm->get('Cases_Item', $reg->case_id)->delete();
			}
			$bookings = $this->orm->get('Booking')->where('patient_id', $patient->id);
			foreach ($bookings->find_all() as $booking) {
				$booking->delete();
			}
			$patient->delete();
		} catch (\Exception $e) {
			$this->rollback();
			throw $e;
		}

		$this->commit();
	}

	public function deleteOnlyWithBookings($patient)
	{
		$this->beginTransaction();
		try {
			$bookings = $this->orm->get('Booking')->where('patient_id', $patient->id);
			foreach ($bookings->find_all() as $booking) {
				$booking->delete();
			}
			$patient->delete();
		} catch (\Exception $e) {
			$this->rollback();
			throw $e;
		}

		$this->commit();
	}
}
