<?php

namespace OpakeAdmin\Helper\Import;

use Opake\Model\Cases\Registration;
use Opake\Helper\TimeFormat;
use PHPExcel_Shared_Date;

class Cases extends AbstractImport
{
	const START_ROW_DATA = 2;

	public function load($filename, $orgId, $type, $location)
	{
		$service = $this->pixie->services->get('cases');
		$objPHPExcel = $this->readFromExcel($filename);

		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();

		$this->pixie->db->begin_transaction();
		try {
			for ($i = self::START_ROW_DATA; $i <= $highestRow; $i++) {
				$mrn = ltrim($sheet->getCell('A' . $i)->getValue(), '0');
				$date = $sheet->getCell('B' . $i)->getValue();
				if (!is_string($date)) {
					$dateObj = new \DateTime('@' . PHPExcel_Shared_Date::ExcelToPHP($date));
					$date = \Opake\Helper\TimeFormat::formatToDB($dateObj);
				}
				$startTime = $sheet->getCell('C' . $i)->getFormattedValue();
				$durationHours = $sheet->getCell('D' . $i)->getValue();
				$durationMinutes = $sheet->getCell('E' . $i)->getValue();
				$surgeonIds = str_replace('.', ',', $sheet->getCell('F' . $i)->getCalculatedValue());
				$description = $sheet->getCell('G' . $i)->getValue();

				if ($mrn) {
					$model = $this->pixie->orm->get('Cases_Item');
					$model->organization_id = $orgId;
					$model->type_id = $type->id;
					$model->location_id = $location->id;
					$model->description = $description;

					$patientModel = $this->pixie->orm->get('Patient');
					$userModel = $this->pixie->orm->get('User');

					$patient = $patientModel->where(['organization_id', $orgId], [$this->pixie->db->expr("CONCAT_WS('-',mrn,mrn_year)"), $mrn])->find();
					if (!$patient->loaded()) {
						throw new \Exception('Unknown patient with mrn ' . $mrn);
					}

					try {
						$startDateTime = new \DateTime($date . ' ' . $startTime);
					} catch (\Exception $e) {
						throw new \Exception('Incorrect DOS/Start Time: ' . $date . ' ' . $startTime);
					}
					$model->time_start = TimeFormat::formatToDBDatetime($startDateTime);

					if ($durationHours) {
						$startDateTime->modify('+' . $durationHours . ' hour');
					}
					if ($durationMinutes) {
						$startDateTime->modify('+' . $durationMinutes . ' minutes');
					}
					$model->time_end = TimeFormat::formatToDBDatetime($startDateTime);

					$users = $userModel->where(
						['organization_id', $orgId],
						['id', 'IN', $this->pixie->db->expr('(' . $surgeonIds . ')')]
					)->find_all()->as_array();

					if (empty($users)) {
						throw new \Exception('Unknown surgeons: ' . $surgeonIds);
					}

					$model->fire_events = false;
					$model->save();

					foreach ($users as $user) {
						$model->add('users', $user);
					}

					$registration = $this->pixie->orm->get('Cases_Registration');
					$registration->case_id = $model->id;
					$registration->status = Registration::STATUS_SUBMIT;
					$registration->fromPatient($patient);
					$registration->save();

					$service->createReports($model);
				}
			}

			$this->pixie->db->commit();
		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			throw $e;
		}
	}

}
