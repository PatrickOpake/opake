<?php

namespace Console;

use Exception;
use Opake\Helper\Currency;
use Opake\Helper\TimeFormat;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Generator;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271\ValidationChecker;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Parser;
use OpakeAdmin\Service\Navicure\HealthCare\Request;
use OpakeAdmin\Service\Navicure\HealthCare\RequestParams;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;

class CLI
{

	/**
	 * DI Pattern
	 * @var \Console\Application
	 */
	protected $pixie;

	/**
	 * ORM module
	 * @var \PHPixie\ORM
	 */
	protected $orm;

	/**
	 * @param \Console\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
		$this->orm = $pixie->orm;
	}

	public function minute()
	{
		$this->pixie->events->fireEvent('timer.minute', null);
		echo "done\n";
	}

	public function minute10()
	{
		$this->pixie->events->fireEvent('timer.minute10', null);
		echo "done\n";
	}

	public function hour()
	{
		$this->pixie->events->fireEvent('timer.hour', null);
		echo "done\n";
	}

	public function day()
	{
		$this->pixie->events->fireEvent('timer.day', null);
		echo "done\n";
	}

	public function importFromExcel()
	{
		return;

		$fname = '_tmp/ICD 10 Codes 2016 UPDATED.xlsx';
		$start_row_data = 1;
		$model_path = 'Cases_Coding_ICD';

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

		$this->pixie->db->get()->execute('START TRANSACTION');
		for ($row = $start_row_data; $row <= $highestRow; $row++) {
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true);
			$model = $this->orm->get($model_path);

			$data = $rowData[0];

			$filtered_array = array_filter($rowData[0]);
			if (!empty($filtered_array)) {
				if (isset($data[0])) {
					$model->code = $data[0];
				}
				if (isset($data[1])) {
					$model->desc = $data[1];
				}

				try {
					$model->save();

				} catch (\Exception $e) {
					$this->pixie->db->get()->execute('ROLLBACK');
					throw new \Exception($e->getMessage());
				}
			}
		}
		$this->pixie->db->get()->execute('COMMIT');


	}

	public function updateICDsFromExcel()
	{
		$fname = '_tmp/ICD_10_Codes_2016_UPDATED_PROPERLY_FORMATTED.xlsx';
		$start_row_data = 1;

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

		$this->pixie->db->get()->execute('START TRANSACTION');
		for ($row = $start_row_data; $row <= $highestRow; $row++) {
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true);

			$data = $rowData[0];
			$filtered_array = array_filter($rowData[0]);
			if (!empty($filtered_array)) {
				try {
					echo $row;
					$this->pixie->db->get()->execute("UPDATE `icd` SET `code` = ? WHERE `code` = ?", [$data[2], $data[0]]);
				} catch (\Exception $e) {
					$this->pixie->db->get()->execute('ROLLBACK');
					throw new \Exception($e->getMessage());
				}
			}
		}
		$this->pixie->db->get()->execute('COMMIT');
	}

	public function importCptsFromExcel()
	{
		return;

		$this->pixie->db->get()->execute('TRUNCATE TABLE `cpt`;');

		$fname = '_tmp/cpt_codes_2016.xlsx';
		$start_row_data = 2;

		$inputFileType = PHPExcel_IOFactory::identify($fname);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($fname);

		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		$this->pixie->db->get()->execute('START TRANSACTION');
		for ($row = $start_row_data; $row <= $highestRow; $row++) {
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
			$model = $this->pixie->orm->get('CPT');

			$data = $rowData[0];
			$filtered_array = array_filter($rowData[0]);
			if (!empty($filtered_array)) {
				if (isset($data[1])) {
					$model->code = $data[1];
				}
				if (isset($data[3])) {
					$model->name = $data[3];
				}
				$model->active = true;

				try {
					$model->save();
				} catch (\Exception $e) {
					$this->pixie->db->get()->execute('ROLLBACK');
					throw new \Exception($e->getMessage());
				}
			}
		}
		$this->pixie->db->get()->execute('COMMIT');
	}

	public function updateCptsFromExcel02($organizationId)
	{
		$this->pixie->db->begin_transaction();

		try {

			$this->pixie->db->get()->execute("UPDATE `case_type` SET `active` = 0 WHERE `organization_id` = ?", [$organizationId]);

			$inputFile = realpath(__DIR__) . '/../../_tmp/cpt_05_2016.xls';
			$startRow = 2;

			$inputFileType = PHPExcel_IOFactory::identify($inputFile);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFile);

			$sheet = $objPHPExcel->getActiveSheet();
			$highestRow = $sheet->getHighestRow();

			for ($i = $startRow; $i <= $highestRow; $i++) {
				$procedureName = $sheet->getCell('A' . $i)->getValue();
				$cptCode = $sheet->getCell('B' . $i)->getValue();

				if (!$procedureName && !$cptCode) {
					break;
				}

				if (!$procedureName) {
					$procedureName = 'N/A';
				}

				$this->pixie->db->query('insert')
					->table('case_type')
					->data([
						'organization_id' => $organizationId,
						'name' => $procedureName,
						'code' => $cptCode,
						'active' => 1
					])->execute();
			}

			$this->pixie->db->commit();

			print "Done" . "\r\n";

		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			throw $e;
		}

	}

	public function updateCptsFromExcel03($organizationId)
	{
		$this->pixie->db->begin_transaction();

		try {

			$this->pixie->db->get()->execute("UPDATE `case_type` SET `active` = 0 WHERE `organization_id` = ?", [$organizationId]);

			$inputFile = realpath(__DIR__) . '/../../_tmp/ClinicianDescriptor_06_2016.xlsx';
			$startRow = 2;

			$inputFileType = PHPExcel_IOFactory::identify($inputFile);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFile);

			$sheet = $objPHPExcel->getActiveSheet();
			$highestRow = $sheet->getHighestRow();

			for ($i = $startRow; $i <= $highestRow; $i++) {
				$procedureName = $sheet->getCell('D' . $i)->getValue();
				$cptCode = $sheet->getCell('B' . $i)->getValue();

				if (!$procedureName && !$cptCode) {
					break;
				}

				if (!$procedureName) {
					$procedureName = 'N/A';
				}

				$this->pixie->db->query('insert')
					->table('case_type')
					->data([
						'organization_id' => $organizationId,
						'name' => $procedureName,
						'code' => $cptCode,
						'active' => 1
					])->execute();
			}

			$this->pixie->db->commit();

			print "Done" . "\r\n";

		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			throw $e;
		}

	}

	public function clearInventory()
	{
		$models = $this->pixie->orm->get('Inventory')
			->find_all();

		foreach ($models as $model) {
			if ($model->loaded()) {
				$model->delete();
			}
		}

		print "Done" . "\r\n";
	}

	public function clearIncorrectDocumentForms()
	{
		$this->pixie->db->begin_transaction();

		try {

			$models = $this->pixie->orm->get('Cases_Registration_Document')
				->with('file')
				->find_all();

			foreach ($models as $model) {
				if ($model->uploaded_file_id && (!$model->file || !$model->file->loaded() || !$model->file->isFileExists())) {
					$model->delete();
					continue;
				}

				if (!$model->uploaded_file_id) {
					$form = $this->pixie->orm->get('Forms_Document')
						->where('doc_type_id', $model->document_type)
						->find();
					if (!$form->loaded()) {
						$model->delete();
					}
				}
			}

			$this->pixie->db->commit();

			print "Done" . "\r\n";

		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			throw $e;
		}

	}

	public function testNavicure()
	{
		$regInsuranceId = 676;
		$regId = 1014;
		$orgId = 17;
		$model = $this->pixie->orm->get('Cases_Registration_Insurance', $regInsuranceId);
		$insuranceDataModel = $model->getInsuranceDataModel();
		$formData = [
			'policy_num' => $insuranceDataModel->policy_number,
			'first_name' => $insuranceDataModel->first_name,
			'last_name' => $insuranceDataModel->last_name,
			'dob' => $insuranceDataModel->dob,
			'organization_id' => $orgId,
			'payor_id' => $insuranceDataModel->insurance_id,
			'type' => $model->type,
			'relationship_to_insured' => $insuranceDataModel->relationship_to_insured
		];
		if ($insuranceDataModel->relationship_to_insured == 0) {
			$caseRegistration = $this->pixie->orm->get('Cases_Registration', $regId);
			if ($caseRegistration->loaded()) {
				$formData['first_name'] = $caseRegistration->first_name;
				$formData['last_name'] = $caseRegistration->last_name;
				$formData['dob'] = $caseRegistration->dob;
			}
		}

		$requestParams = new RequestParams(
			$this->pixie,
			$formData['policy_num'],
			$formData['first_name'],
			$formData['last_name'],
			$formData['dob'],
			$formData['organization_id'],
			$formData['type'],
			$formData['payor_id'],
			$formData['relationship_to_insured']
		);
		$generator = new Generator($requestParams);
		$navicureRequest = new Request($this->pixie, $generator->getEDI());
		$response =  $navicureRequest->getResponse();
		try {
			if(empty($response)) {
				throw new \Exception('Response is empty');
			}
			if($response->statusHeader->requestProcessed && $response->payload) {
				$doc = new  Parser($response->payload);
				$benefit = $doc->toArray();

				$model = $this->pixie->orm->get('Eligible_CaseCoverage');
				$model->case_registration_id = $regId;
				$model->case_insurance_id = $regInsuranceId;
				$model->coverage = json_encode($benefit);
				$model->updated = TimeFormat::formatToDBDatetime(new \DateTime());

				$json = $model->getCoverageArray();

				file_put_contents('testFile', print_r($json, true));
				$previousModel = $this->pixie->orm->get('Eligible_CaseCoverage')
					->where('case_registration_id', $regId)
					->find();

				if ($previousModel->loaded()) {
					$previousModel->delete();
				}

				$model->save();
			}
		} catch(\Exception $e) {
			throw new \Exception($e->getMessage());
		}
	}

	public function testNavicureValidation()
	{
		$x12 = 'ISA*00*          *00*          *ZZ*MEDDATA        *ZZ*MEDDATA        *131212*1240*^*00501*000000001*0*P*:~
			GS*HB*MEDDATA*MEDDATA*20131212*124058*1*X*005010X279A1~
			ST*271*0001*005010X279A1~
			BHT*0022*11*ABC123*20131212*124107~
			HL*1**20*1~
			AAA*Y**42*Y~
			NM1*PR*2*UNITEDHEALTHCARE*****PI*ABC123~
			PER*IC**UR*AAABBBCCCC~
			HL*2*1*21*1~
			NM1*1P*2*SMITH*****XX*ABC123~
			AAA*N**43*C~
			HL*3*2*22*0~
			TRN*2*ABC123*9MEDDATACO~
			NM1*IL*1*SMITH*JOHN*S***MI*ABC123~
			REF*6P*ABC123~
			N3*123 RIDGE WAY~
			N4*CHARLOTTE*NC*28211~
			AAA*N**72*C~
			DMG*D8*19900101*M~
			INS*Y*18*001*25~
			DTP*346*D8*19900101~
			EB*1**30*C1*CHOICE PLUS~
			AAA*N**70*C~
			LS*2120~
			NM1*PR*2*SMITH*****PI*ABC123~
			N3*123 RIDGE WAY~
			N4*CHARLOTTE*NC*28211~
			PER*IC**UR*AAABBBCCCC~
			LE*2120~
			EB*C*FAM*30****0*****W~
			EB*C*IND*30****0*****W~
			EB*G*FAM*30*C1**23*0*****W~
			MSG*ADDITIONAL COVERED PER OCCURRENCE~
			EB*X~
			LS*2120~
			NM1*1P*2*SMITH*****XX*ABC123~
			LE*2120~
			SE*53*0001~
			GE*1*1~
			IEA*1*000000001~';

		$doc = new  Parser($x12);
		$validation = new ValidationChecker($doc->getBenefit());
		$errors = $validation->validate();
		echo '<pre>', var_dump($errors), '</pre>';
	}


	public function checkLogger()
	{
		$this->pixie->logger->info('Check logger');
	}

	public function collectStatic()
	{
		$adminApp = new \OpakeAdmin\Application();
		$adminApp->bootstrap($this->pixie->root_dir);
		$view = $adminApp->view('main');
		$view->setForceCompileAndMinify(true);
		$view->setDefaultJsCss();
		$view->getCssHtml();
		$view->getJsHtml();


		$patientApp = new \OpakePatients\Application();
		$patientApp->bootstrap($this->pixie->root_dir);
		$view = $patientApp->view('main');
		$view->setForceCompileAndMinify(true);
		$view->setDefaultJsCss();
		$view->getCssHtml();
		$view->getJsHtml();

		echo "done\r\n";
	}

	public function checkPrintChart()
	{
		$case = $this->pixie->orm->get('Cases_Item', 216);
		$chartGroup = $this->orm->get('Forms_ChartGroup')
			->where('id', 18)
			->find();

		$documentsToPrint = [];
		foreach ($chartGroup->getDocuments() as $document) {
			$documentsToPrint[] = \OpakeAdmin\Helper\Printing\Document\Cases\Chart::createDocument($document, $case);
		}

		$_SERVER['HTTP_HOST'] = 'opakeadmin.dev.rokolabs.com';
		$helper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
		$printResult = $helper->compile($documentsToPrint);
	}

	public function checkPDFDynamicVariables()
	{
		$case = $this->pixie->orm->get('Cases_Item', 541);
		$pdf = '/srv/own/Example.pdf';
		$resultPdf = '/srv/own/Example-Result.pdf';

		$models = $this->pixie->orm->get('Forms_PDF_DynamicField')
			->where('doc_id', 98)
			->find_all();

		$variables = [];
		foreach ($models as $model) {
			$variables[$model->page][] = [
				$model->name,
				$model->x,
				$model->y,
				$model->width,
				$model->height
			];
		}

		$writer = new \OpakeAdmin\Helper\Chart\PDF\DynamicFieldsWriter($pdf, $variables);
		$writer->setCase($case);
		$writer->setOutputFilePath($resultPdf);
		$writer->writeFields();
	}

	public function importDischargeConditionAndOccurrenceCodesFromExcel()
	{
		$this->importDischargeCodesFromExcel();
		$this->importConditionCodesFromExcel();
		$this->importOccurrenceCodesFromExcel();
	}

	protected function importDischargeCodesFromExcel()
	{
		$fname = '_tmp/discharge_codes.xlsx';
		$start_row_data = 2;

		$inputFileType = PHPExcel_IOFactory::identify($fname);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($fname);

		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		$this->pixie->db->get()->execute('TRUNCATE TABLE `discharge_status_code`;');

		$this->pixie->db->get()->execute('START TRANSACTION');
		for ($row = $start_row_data; $row <= $highestRow; $row++) {
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
			$model = $this->pixie->orm->get('DischargeStatusCode');

			$data = $rowData[0];
			$filtered_array = array_filter($rowData[0]);
			if (!empty($filtered_array)) {
				if (isset($data[0])) {
					$model->code = $data[0];
				}
				if (isset($data[1])) {
					if (is_string($data[1])) {
						$model->effective_date = \Opake\Helper\TimeFormat::formatToDB(new \DateTime($data[1]));
					} else {
						$dateObj = new \DateTime('@' . PHPExcel_Shared_Date::ExcelToPHP($data[1]));
						$model->effective_date = \Opake\Helper\TimeFormat::formatToDB($dateObj);
					}
				}
				if (isset($data[2])) {
					if (is_string($data[2])) {
						$model->change_date = \Opake\Helper\TimeFormat::formatToDB(new \DateTime($data[2]));
					} else {
						$dateObj = new \DateTime('@' . PHPExcel_Shared_Date::ExcelToPHP($data[2]));
						$model->change_date = \Opake\Helper\TimeFormat::formatToDB($dateObj);
					}
				}
				if (isset($data[3])) {
					if (is_string($data[2])) {
						$model->delete_date = \Opake\Helper\TimeFormat::formatToDB(new \DateTime($data[3]));
					} else {
						$dateObj = new \DateTime('@' . PHPExcel_Shared_Date::ExcelToPHP($data[3]));
						$model->delete_date = \Opake\Helper\TimeFormat::formatToDB($dateObj);
					}
				}
				if (isset($data[4])) {
					$model->verbiage = $data[4];
				}

				try {
					$model->save();
				} catch (\Exception $e) {
					$this->pixie->db->get()->execute('ROLLBACK');
					throw new \Exception($e->getMessage());
				}
			}
		}
		$this->pixie->db->get()->execute('COMMIT');
	}

	protected function importConditionCodesFromExcel()
	{
		$fname = '_tmp/condition_codes.xlsx';
		$start_row_data = 2;

		$inputFileType = PHPExcel_IOFactory::identify($fname);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($fname);

		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		$this->pixie->db->get()->execute('TRUNCATE TABLE `condition_code`;');

		$this->pixie->db->get()->execute('START TRANSACTION');
		for ($row = $start_row_data; $row <= $highestRow; $row++) {
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
			$model = $this->pixie->orm->get('ConditionCode');

			$data = $rowData[0];
			$filtered_array = array_filter($rowData[0]);
			if (!empty($filtered_array)) {
				if (isset($data[0])) {
					$model->code = $data[0];
				}
				if (isset($data[1])) {
					$model->description = $data[1];
				}

				try {
					$model->save();
				} catch (\Exception $e) {
					$this->pixie->db->get()->execute('ROLLBACK');
					throw new \Exception($e->getMessage());
				}
			}
		}
		$this->pixie->db->get()->execute('COMMIT');
	}

	protected function importOccurrenceCodesFromExcel()
	{
		$fname = '_tmp/occurrence_codes.xlsx';
		$start_row_data = 2;

		$inputFileType = PHPExcel_IOFactory::identify($fname);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($fname);

		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		$this->pixie->db->get()->execute('TRUNCATE TABLE `occurrence_code`;');

		$this->pixie->db->get()->execute('START TRANSACTION');
		for ($row = $start_row_data; $row <= $highestRow; $row++) {
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
			$model = $this->pixie->orm->get('OccurrenceCode');

			$data = $rowData[0];
			$filtered_array = array_filter($rowData[0]);
			if (!empty($filtered_array)) {
				if (isset($data[0])) {
					$model->code = $data[0];
				}
				if (isset($data[1])) {
					$model->description = $data[1];
				}

				try {
					$model->save();
				} catch (\Exception $e) {
					$this->pixie->db->get()->execute('ROLLBACK');
					throw new \Exception($e->getMessage());
				}
			}
		}
		$this->pixie->db->get()->execute('COMMIT');
	}

	public function importValueCodesFromExcel()
	{
		$fname = '_tmp/value_codes.xlsx';
		$start_row_data = 4;

		$inputFileType = PHPExcel_IOFactory::identify($fname);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($fname);

		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		$this->pixie->db->get()->execute('START TRANSACTION');
		for ($row = $start_row_data; $row <= $highestRow; $row++) {
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
			$model = $this->pixie->orm->get('ValueCode');

			$data = $rowData[0];
			$filtered_array = array_filter($rowData[0]);
			if (!empty($filtered_array)) {
				if (isset($data[0])) {
					$model->code = $data[0];
				}
				if (isset($data[1])) {
					$model->description = $data[1];
				}

				try {
					$model->save();
				} catch (\Exception $e) {
					$this->pixie->db->get()->execute('ROLLBACK');
					throw new \Exception($e->getMessage());
				}
			}
		}
		$this->pixie->db->get()->execute('COMMIT');
	}

	public function importCpts2017FromExcel($organizationId)
	{
		$this->pixie->db->begin_transaction();

		try {
			$inputFile = realpath(__DIR__) . '/../../_tmp/2017CPT_ClinicianDescriptor.xlsx';
			$startRow = 2;

			$inputFileType = PHPExcel_IOFactory::identify($inputFile);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFile);

			$sheet = $objPHPExcel->getActiveSheet();
			$highestRow = $sheet->getHighestRow();

			for ($i = $startRow; $i <= $highestRow; $i++) {
				$procedureName = $sheet->getCell('D' . $i)->getValue();
				$cptCode = $sheet->getCell('B' . $i)->getValue();

				if (!$procedureName && !$cptCode) {
					break;
				}

				if (!$procedureName) {
					$procedureName = 'N/A';
				}

				$caseType = $this->orm->get('Cases_Type')->where('code', $cptCode)->where('name', $procedureName)->order_by('id', 'desc')->limit(1)->find();

				if ($caseType->loaded()) {
					$caseType->is_2017 = 1;
					$caseType->active = 1;
					$caseType->save();
				} else {
					$this->pixie->db->query('insert')
						->table('case_type')
						->data([
							'organization_id' => $organizationId,
							'name' => $procedureName,
							'code' => $cptCode,
							'active' => 1,
							'is_2016' => 0,
							'is_2017' => 1
						])->execute();
				}
			}

			$this->pixie->db->commit();

			print "Done" . "\r\n";

		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			throw $e;
		}

	}

	public function importICDs2017FromExcel()
	{
		$fname = '_tmp/2017_ICD10_ProperlyFormatted.xlsx';
		$start_row_data = 1;

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

		$this->pixie->db->get()->execute('START TRANSACTION');
		for ($row = $start_row_data; $row <= $highestRow; $row++) {
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true);

			$data = $rowData[0];
			$filtered_array = array_filter($rowData[0]);
			if (!empty($filtered_array)) {
				try {
					$code =  $data[0];
					$desc =  $data[1];

					$icd = $this->orm->get('ICD')->where('code', $code)->where('desc', $desc)->find();

					if ($icd->loaded()) {
						$icd->is_2017 = true;
						$icd->save();
					} else {
						$this->pixie->db->query('insert')
							->table('icd')
							->data([
								'code' => $data[0],
								'desc' => $data[1],
								'is_2016' => 0,
								'is_2017' => 1
							])->execute();
					}
				} catch (\Exception $e) {
					$this->pixie->db->get()->execute('ROLLBACK');
					throw new \Exception($e->getMessage());
				}
			}
		}
		$this->pixie->db->get()->execute('COMMIT');
	}

	public function fixFullUrlsInCharts()
	{
		$replacements = [
			'opakeadmin.dev.rokolabs.com' => 'opake.dev.rokolabs.com',
		    'opakeadmin.qa.rokolabs.com' => 'opake.qa.rokolabs.com',
		    'opakeadmin.stage.rokolabs.com' => 'opake.stage.rokolabs.com',
		    'admin.opake.com' => 'opake.com',
		];

		$this->pixie->db->begin_transaction();

		try {
			$rows = $this->pixie->db->query('select')
				->table('forms_document')
				->fields('own_text', 'id')
				->where('own_text', 'IS NOT NULL', $this->pixie->db->expr(''))
				->execute();

			foreach ($rows as $row) {
				$text = $row->own_text;
				foreach ($replacements as $search => $rep) {
					$text = str_ireplace($search, $rep, $text);
				}

				$this->pixie->db->query('update')
					->table('forms_document')
					->data([
						'own_text' => $text
					])
					->where('id', $row->id)
					->execute();
			}

			$this->pixie->db->commit();
		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			throw $e;
		}

		print "Done" . "\r\n";
	}

	public function updateNavicurePayorsList()
	{
		$existedCompanyNames = [];
		$q = $this->pixie->db->query('select')
				->table('insurance_payor')
				->fields('name')
				->where('actual', 1)
				->execute();

		foreach ($q as $row) {
			$existedCompanyNames[strtolower($row->name)] = $row->name;
		}

		$inputFile = realpath(__DIR__) . '/../../_tmp/Navicure_FullPayerList_Addresses.xlsx';
		$outputFile = realpath(__DIR__) . '/../../_tmp/Navicure_FullPayerListAddresses_Updated.xlsx';
		$stateFile = realpath(__DIR__) . '/../../_tmp/navicure_state';
		$startRow = 2;

		$inputFileType = PHPExcel_IOFactory::identify($inputFile);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($inputFile);

		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();

		for ($i = $startRow; $i <= $highestRow; $i++) {
			$companyName = $sheet->getCell('A' . $i)->getValue();
			$lowerCaseName = strtolower($companyName);
			if (isset($existedCompanyNames[$lowerCaseName])) {
				print "Company \"" . $companyName . "\" already in DB\r\n" ;
				$sheet->getCell('K' . $i)->setValue($existedCompanyNames[$lowerCaseName]);
				file_put_contents($stateFile, $i . '|' . $companyName . '|' . $existedCompanyNames[$lowerCaseName] . "\r\n", FILE_APPEND);
				continue;
			}

			$weightedCompanyNames = [];
			foreach ($existedCompanyNames as $lowerDBCompanyName => $dbCompanyName) {
				$weightedCompanyNames[] = [levenshtein($lowerCaseName, $lowerDBCompanyName), $dbCompanyName];
			}

			usort($weightedCompanyNames, function($a, $b) {
				if ($a[0] > $b[0]) {
					return 1;
				}
				if ($a[0] < $b[0]) {
					return -1;
				}

				return 0;
			});

			$weightedCompanyNames = array_splice($weightedCompanyNames, 0, 10);

			print "\r\n\r\n";
			print "Options for \"" . $companyName . "\"\r\n";
			foreach ($weightedCompanyNames as $index => $data) {
				print $index . ". " . $data[1] . " [" . $data[0] . "]\r\n";
			}
			print "N for New company\r\n";

			$choice = readline();

			if (strtoupper($choice) === 'N') {
				print "Skipped\r\n";
				file_put_contents($stateFile, $i . '|' . $companyName . '|' . '' . "\r\n", FILE_APPEND);
				continue;
			}

			if (isset($weightedCompanyNames[$choice])) {
				$selectedCompanyName = $weightedCompanyNames[$choice][1];
				print "Selected: " . $selectedCompanyName . "\r\n";
				$sheet->getCell('K' . $i)->setValue($selectedCompanyName);
				file_put_contents($stateFile, $i . '|' . $companyName . '|' . $selectedCompanyName . "\r\n", FILE_APPEND);
			}


		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
		$objWriter->save($outputFile);

		print "Done\r\n";
	}

	public function checkNavicureIncomingFiles()
	{
		$handler = new \OpakeAdmin\Service\Navicure\Claims\ResponseHandler();
		$handler->handleIncomingFiles();
	}

	public function checkNavicureAcknowledgment()
	{
		$content = "ISA*00*          *00*          *ZZ*810525733      *ZZ*473451322      " .
			"*170303*1738*U*00401*000000001*0*P*:~GS*FA*810525733*473451322*20170303*1738*1*X*005010X222A1~" .
			"ST*997*1~AK1*HC*112049~
			AK2*837*120491~
			AK5*A~
			AK2*837*0002~
			AK3*CLM*22**8~
			AK4*1*1028*1~
			AK5*R*5~
			AK9*A*2*2*2~SE*8*1~GE*1*1~IEA" .
			"*1*000000001~";

		$parser = new \OpakeAdmin\Service\ASCX12\E997\Response\AcknowledgmentParser();
		$segment = $parser->parse($content);

		var_dump($segment);
	}

	public function checkNavicureAcknowledgment2()
	{
		$content2 = "ISA*00*          *00*          *ZZ*NAVICURE       *ZZ*               *170320*1139*^*00404*246671483*1*P*:~GS*HN*NAVICURE*OPAKE*20170320*1139*74146483*X*004040X167~ST*277*246671483*004040X167~BHT*0085*08*246671483*20170320*1139*TH~HL*1**20*1~NM1*AY*2*NAVICURE INC*****FI*582574363~TRN*1*74146483~DTP*050*D8*20170320~DTP*009*D8*20170320~HL*2*1*21*1~NM1*41*2*Opake*****46*000000000~TRN*2*1~STC*A0:19:40:65*20170320*WQ*21.45~HL*3*2*19*1~NM1*85*1*SURGEON*SURGEON****24*123123123~TRN*1*0~STC*A0:19:40:65**WQ*21.45~QTY*QC*1~AMT*YY*21.45~HL*4*3*PT~NM1*QC*1*EINSTEIN*ALBERT****MI*00666~TRN*2*14**MEDICARE AL~STC*A7:21:82:65*20170320*U*21.45********RENDERING TAXONOMY CODE - INVALID FORMAT.~STC*A7:153:85:65*20170320*U*21.45********BILLING NPI - INVALID NPI CHECK DIGIT.~STC*A7:126:QC:65*20170320*U*21.45********Insufficient/ Invalid Patient City (Box 5)~STC*A7:162:IL:65*20170320*U*21.45********INVALID INSURED ID FOR MEDICARE.~STC*A7:0:PR:65*20170320*U*21.45********INVALID SOURCE OF PAY (Box #1) - MUST BE MEDICARE FOR THIS PAYER.~STC*A6:156:QC:65*20170320*U*21.45********MISSING PATIENT RELATIONSHIP TO THE PRIMARY INSURED.~STC*A7:126:QC:65*20170320*U*21.45********INVALID PATIENT STATE.~STC*A7:126:IL:65*20170320*U*21.45********INVALID PRIMARY INSURED ZIP CODE~STC*A6:126:QC:65*20170320*U*21.45********PATIENT ADDRESS, CITY, STATE AND ZIP CODE ARE REQUIRED~STC*A7:126:85:65*20170320*U*21.45********INVALID BILLING PROVIDER STREET ZIP CODE BOX #33 (2010AA)~STC*A7:153:82:65*20170320*U*21.45********FOR THIS PAYER NO PROVIDERS HAVE BEEN APPROVED FOR ELECTRONIC CLAIMS, PLEASE CALL NAVICURE AT 770-342-0800.~STC*A7:126:QC:65*20170320*U*21.45********PATIENT CITY MUST BE AT LEAST 2 ALPHABETIC CHARACTERS IN LENGTH.~STC*A7:153:82:65*20170320*U*21.45********RENDERING PROVIDER NPI - INVALID NPI CHECK DIGIT.~STC*A7:126:QC:65*20170320*U*21.45********STREET ADDRESS REQUIRED IN PATIENT ADDRESS LINE #1.~STC*A7:126:85:65*20170320*U*21.45********BILLING PROVIDER STREET ADDRESS ZIP CODE MUST BE 9 DIGITS. PLEASE CONTACT NAVICURE CLIENT SERVICES FOR ASSISTANCE 770-342-0800 (ANSI 5010)~STC*A7:21:85:65*20170320*U*21.45********BILLING TAXONOMY CODE - INVALID FORMAT.~REF*D9*276VBFlS~DTP*232*RD8*20170310-20170310~SVC*HC:G0120*4~STC*A7:153:82:65*20170320*U*********RENDERING PROVIDER NPI ON SERVICE LINE #1 - INVALID NPI CHECK DIGIT.~SVC*HC:42831*9.22~STC*A7:153:82:65*20170320*U*********RENDERING PROVIDER NPI ON SERVICE LINE #2 - INVALID NPI CHECK DIGIT.~SVC*HC:42836*8.23~STC*A7:153:82:65*20170320*U*********RENDERING PROVIDER NPI ON SERVICE LINE #3 - INVALID NPI CHECK DIGIT.~SE*45*246671483~GE*1*74146483~IEA*1*246671483~";
		$content3 = "ISA*00*          *00*          *ZZ*NAVICURE       *ZZ*               *170328*1539*^*00404*248113954*1*P*:~
GS*HN*NAVICURE*OPAKE*20170328*1539*74457822*X*004040X167~
ST*277*248113954*004040X167~
BHT*0085*08*248113954*20170328*1539*TH~
HL*1**20*1~
NM1*AY*2*NAVICURE INC*****FI*582574363~
TRN*1*74457822~
DTP*050*D8*20170328~
DTP*009*D8*20170328~
HL*2*1*21*1~
NM1*41*2*Opake*****46*000000000~
TRN*2*1~
STC*A0:19:40:65*20170328*WQ*25.63~
HL*3*2*19*1~
NM1*85*1*CAT*CAT****24*615121337~
TRN*1*0~
STC*A0:19:40:65**WQ*25.63~
QTY*QC*1~
AMT*YY*25.63~
HL*4*3*PT~
NM1*QC*1*NEWTON*ISAAC****MI*X12344~
TRN*2*5**UNITED HEALTHCARE~
STC*A7:19::65*20170328*U*25.63~
REF*D9*2H6THDxm~
DTP*232*RD8*20170301-20170301~
SVC*HC:10022*9.22~
SVC*HC:10040*3.14~
SVC*HC:10060:A:B:C*4.15~
STC*A7:453:40:65*20170328*U*********PROCEDURE CODE MODIFIER #3 ON LINE #3 MUST CONTAIN TWO ALPHANUMERIC CHARACTERS.~
STC*A7:453:40:65*20170328*U*********PROCEDURE CODE MODIFIER #1 ON LINE #3 MUST CONTAIN TWO ALPHANUMERIC CHARACTERS.~
STC*A7:453:40:65*20170328*U*********PROCEDURE CODE MODIFIER #2 ON LINE #3 MUST CONTAIN TWO ALPHANUMERIC CHARACTERS.~
SVC*HC:10081*9.12~
SE*31*248113954~
GE*1*74457822~
IEA*1*248113954~";
		$parser = new \OpakeAdmin\Service\ASCX12\E277\Response\AcknowledgmentParser();
		$segment = $parser->parse($content3);
		print_R($segment);

	}

	public function checkNavicureAcknowledgment3()
	{
		$content = "
			ISA*00*          *00*          *30*NAVICURE1      *30*631192386      *111108*1500*^*00501*009138581*1*P*:~
			GS*HP*1352440000*1000000*20111108*1500*9138581*X*005010X221A1~
			ST*835*000000001~
			BPR*I*0*C*CHK************20111021~
			TRN*1*3041439620*1352440000~
			DTM*405*20111021~
			N1*PR*BLUE CROSS BLUE SHIELD OF ALABAMA~
			N3*450 RIVERCHASE PKWY EAST~
			N4*BIRMINGHAM*AL*35244~
			REF*2U*00510~
			PER*CX**TE*8006347592~
			PER*BL*EDI SERVICES*EM*ASK-EDI@BCBSAL.ORG*TE*2052206899~
			N1*PE*DR JOHN*XX*1111111111~
			N3*PO BOX 123~
			N4*JOY*AL*357730129~
			REF*TJ*631192386~
			RDM*OL*NAVICURE*WWW.NAVICURE.COM~
			LX*1~
			TS3*1093750804*11*20111231*1*95~
			CLP*483110*2*95*0**12*7182919955*11~
			NM1*QC*1*SMITH*JOHN*R***MI*999999999~
			NM1*74*1**SMITH*L***C*FMR999999~
			DTM*232*20101018~
			DTM*233*20101018~
			DTM*036*20100101~
			DTM*050*20111018~
			SVC*HC:99214*95*0****0~
			DTM*472*20101018~
			CAS*OA*204*19**23*76~
			REF*6R*000001~
			SE*26*000000001~
			GE*1*9138581~
			IEA*1*009138581~
		";

		$parser = new \OpakeAdmin\Service\ASCX12\E835\Response\ClaimPaymentParser();
		$segment = $parser->parse($content);
		print_r($segment->getFirstChildSegment()->getFirstChildSegment());
		print_R($segment);
	}

	public function checkNavicureEl()
	{
		$content = "ISA*00*          *00*          *01*OPAKE          *ZZ*NAVICURE       *170331*0800* *00501*003310800*0*T* ~
				GS*HS*OPAKE          *NAVICURE       *20170331*0800*903310800*X*005010X279A1~
				ST*270*005010X279A1~
				BHT*0022*13**20170331*0800~
				HL*1**2O*0~
				NM1*PR*2*UNITED HEALTHCARE*****PI*10002~
				HL*2*1*21*1~
				NM1*1P*2*INTERNATIONAL CENTER FOR MINIMALLY INVASIVE SPINE SURGERY1*****SV*1231231231~
				HL*3*2*22*1~
				TRN*1*03310800*03310800~
				NM1*IL*1*PIETANZA*JOZEPHINE****MI*648408400~
				EQ*30~
				HL*4*3*23*0~
				TRN*1*03310810*03310801~
				NM1*03*1*WEYMOUTH*AARON~
				DMG*D8*19860402~
				EQ*30~
				SE*18*03310800~
				GE*1*903310800~
				IEA*1*003310800~
		";

		$request =  new \OpakeAdmin\Service\Navicure\HealthCare\Request($this->pixie, $content);
		$response =  $request->getResponse();
		if(empty($response)) {
			throw new \Exception('Response is empty');
		}
		var_dump($response);
		if ($response->statusHeader->requestProcessed && $response->payload) {
			var_dump($response->payload);
		}
	}

	public function checkInboundEfaxes()
	{
		$faxService = new \OpakeAdmin\Service\Scrypt\SFax\FaxService();
		$faxService->checkInboundFaxes();
	}

	public function downloadEfax()
	{
		$faxService = new \OpakeAdmin\Service\Scrypt\SFax\FaxService();
		$faxService->downloadInboundFax($this->pixie->orm->get('Efax_InboundFax', 2));
	}

	public function checkNavicureClaimsProcessing()
	{
		$claimId = 130;

		$text = "
		ISA*00*          *00*          *30*NAVICURE1      *30*631192386      *111108*1500*^*00501*009138581*1*P*:~
		GS*HP*1352440000*1000000*20111108*1500*9138581*X*005010X221A1~
		ST*835*000000001~
		BPR*I*0*C*CHK************20111021~
		TRN*1*3041439620*1352440000~
		DTM*405*20111021~
		N1*PR*BLUE CROSS BLUE SHIELD OF ALABAMA~
		N3*450 RIVERCHASE PKWY EAST~
		N4*BIRMINGHAM*AL*35244~
		REF*2U*00510~
		PER*CX**TE*8006347592~
		PER*BL*EDI SERVICES*EM*ASK-EDI@BCBSAL.ORG*TE*2052206899~
		N1*PE*DR JOHN*XX*1111111111~
		N3*PO BOX 123~
		N4*JOY*AL*357730129~
		REF*TJ*631192386~
		RDM*OL*NAVICURE*WWW.NAVICURE.COM~
		LX*1~
		TS3*1093750804*11*20111231*1*95~
		CLP*" . $claimId . "*2*115*21**12*7182919955*11~
		NM1*QC*1*SMITH*JOHN*R***MI*999999999~
		NM1*74*1**SMITH*L***C*FMR999999~
		DTM*232*20101018~
		DTM*233*20101018~
		DTM*036*20100101~
		DTM*050*20111018~
		SVC*HC:99214*95*21****0~
		DTM*472*20101018~
		CAS*OA*204*19**23*76~
		CAS*PR*1*6**2*4**3*10~
		SVC*HC:99999*121*92*12*5**0~
		DTM*472*20101018~
		CAS*OA*Y3*5*~
		CAS*PR*1*6**2*4**3*10~
		REF*6R*000001~
		SE*26*000000001~
		GE*1*9138581~
		IEA*1*009138581~
		";

		$file = new \OpakeAdmin\Service\Navicure\Claims\IncomingFiles\E835ClaimPayment($text);

		$handler = new \OpakeAdmin\Service\Navicure\Claims\ResponseHandler();
		$handler->handleSingleIncomingFile($file);
	}

	public function fixFullUrlsInChartsNewHosts()
	{
		$replacements = [
			'opake.qa.rokolabs.com' => 'qa.opake.com',
			'opake.stage.rokolabs.com' => 'staging.opake.com',
		];

		$this->pixie->db->begin_transaction();

		try {
			$rows = $this->pixie->db->query('select')
				->table('forms_document')
				->fields('own_text', 'id')
				->where('own_text', 'IS NOT NULL', $this->pixie->db->expr(''))
				->execute();

			foreach ($rows as $row) {
				$text = $row->own_text;
				foreach ($replacements as $search => $rep) {
					$text = str_ireplace($search, $rep, $text);
				}

				$this->pixie->db->query('update')
					->table('forms_document')
					->data([
						'own_text' => $text
					])
					->where('id', $row->id)
					->execute();
			}

			$this->pixie->db->commit();
		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			throw $e;
		}

		print "Done" . "\r\n";
	}

	public function checkNavicureClaimsProcessing2()
	{
		$text = "
			ISA*00*          *00*          *30*582574363      *30*261771662      *171201*0613*^*00501*290356345*0*P*:~
			GS*HP*1237391136*1000000*20171201*0613*290356345*X*005010X221A1~
			ST*835*000000001~
			BPR*H*0*C*NON************20171201~
			TRN*1*362927198*1237391136~
			REF*EV*1923054~
			DTM*405*20171130~
			N1*PR*NOVITAS SOLUTIONS, INC.~
			N3*PO BOX 3031~
			N4*MECHANICSBURG*PA*170551803~
			REF*2U*12402~
			PER*CX**TE*8772358073~
			PER*BL*NOVITAS SOLUTIONS, INC. EDI SERVICES*TE*8772358073*EX*1*EM*WEBSITEEDI@NOVITAS-SOLUTIONS.COM~
			PER*IC**UR*WWW.NOVITAS-SOLUTIONS.COM~
			N1*PE*Millennium Healthcare of Clifton*XX*1528347101~
			N3*SUITE 201*925 CLIFTON AVENUE~
			N4*CLIFTON*NJ*070132724~
			REF*TJ*261771662~
			LX*1~
			CLP*0011*1*19172*0**MB*0217333622152*24*1~
			NM1*QC*1*xxxxxxxxxx*xxxxx****HN*xxxxxxxxxx~
			MOA***MA15~
			DTM*050*20171129~
			SVC*HC:64493:50*19172*0****2~
			DTM*472*20170630~
			CAS*CO*4*19172~
			REF*LU*24~
			REF*6R*1~
			LQ*HE*M20~
			LQ*HE*N519~
			LQ*HE*MA130~
			CLP*0012*1*9586*0**MB*0217331507930*24*1~
			NM1*QC*1*xxxxxxx*xxxx****HN*xxxxxxxxxx~
			MOA***MA01~
			DTM*050*20171127~
			SVC*HC:62321:LT*4793*0****1~
			DTM*472*20170807~
			CAS*CO*151*4793~
			REF*LU*24~
			REF*6R*1~
			LQ*HE*MA01~
			SVC*HC:62321:RT*4793*0****1~
			DTM*472*20170807~
			CAS*CO*151*4793~
			REF*LU*24~
			REF*6R*2~
			LQ*HE*MA01~
			CLP*0013*1*4793*0**MB*0217332311160*24*1~
			NM1*QC*1*xxxxxxxxxx*xxxx****HN*xxxxxxxxxx~
			MOA***MA01~
			DTM*050*20171127~
			SVC*HC:62321:RT*4793*0****1~
			DTM*472*20170623~
			CAS*CO*50*4793~
			REF*LU*24~
			REF*6R*1~
			REF*0K*L36920~
			LQ*HE*M25~
			LQ*HE*N115~
			CLP*0014*1*4793*0**MB*0217332120050*24*1~
			NM1*QC*1*xxxxxxx*xxxxxx****HN*xxxxxxxxxxx~
			MOA***MA01~
			DTM*050*20171127~
			SVC*HC:62321*4793*0****1~
			DTM*472*20171113~
			CAS*CO*50*4793~
			REF*LU*24~
			REF*6R*1~
			REF*0K*L36920~
			LQ*HE*M25~
			LQ*HE*N115~
			SE*70*000000001~
			ST*835*000000002~
			BPR*I*2880.93*C*ACH*CCP*01*081517693*DA*152302017073*1205296137**01*021201383*DA*000041694163*20171201~
			TRN*1*890883054*1237391136~
			REF*EV*1923054~
			DTM*405*20171130~
			N1*PR*NOVITAS SOLUTIONS, INC.~
			N3*PO BOX 3031~
			N4*MECHANICSBURG*PA*170551803~
			REF*2U*12402~
			PER*CX**TE*8772358073~
			PER*BL*NOVITAS SOLUTIONS, INC. EDI SERVICES*TE*8772358073*EX*1*EM*WEBSITEEDI@NOVITAS-SOLUTIONS.COM~
			N1*PE*Millennium Healthcare of Clifton*XX*1528347101~
			N3*SUITE 201*925 CLIFTON AVENUE~
			N4*CLIFTON*NJ*070132724~
			REF*TJ*261771662~
			LX*1~
			CLP*0015*19*8739*468.02*119.39*MB*0217321652100*24*1~
			NM1*QC*1*xxxxx*xxxxxxx****HN*xxxxxxxxxxxxx~
			NM1*TT*2*UNITEDHEALTH GROUP*****PI*30002~
			MOA***MA01*MA18~
			DTM*050*20171117~
			SVC*HC:26116:RT*8739*468.02**1~
			DTM*472*20170713~
			CAS*CO*45*8142.04**253*9.55~
			CAS*PR*2*119.39~
			REF*LU*24~
			REF*6R*1~
			AMT*B6*596.96~
			CLP*0018*19*30170*708.21*180.66*MB*0917321269150*24*1~
			NM1*QC*1*xxxxxxxxx*xxxxxx****HN*xxxxxxxxxxxx~
			NM1*TT*2*BLUE CROSS OF CALIFORNIA*****PI*00170~
			MOA***MA01*MA18~
			DTM*050*20171117~
			SVC*HC:64450*24172*0****4~
			DTM*472*20170616~
			CAS*CO*B15*24172~
			REF*LU*24~
			REF*6R*1~
			LQ*HE*M80~
			SVC*HC:64635:LT:59*5998*708.21**1~
			DTM*472*20170616~
			CAS*CO*45*5094.68**253*14.45~
			CAS*PR*2*180.66~
			REF*LU*24~
			REF*6R*2~
			AMT*B6*903.32~
			CLP*0019*19*9586*464.92*118.6*MB*0917321269140*24*1~
			NM1*QC*1*xxxxxx*xxxx****HN*xxxxxxxxxxxx~
			NM1*TT*2*TRANSAMERICA PREMIER LIFE INS*****PI*30006~
			MOA***MA01*MA18~
			DTM*050*20171117~
			SVC*HC:64483:LT:51*4793*154.98**1*HC:64483:LT~
			DTM*472*20170619~
			CAS*CO*45*4397.66**253*3.16**59*197.67~
			CAS*PR*2*39.53~
			REF*LU*24~
			REF*6R*1~
			AMT*B6*197.67~
			SVC*HC:64483:RT*4793*309.94**1~
			DTM*472*20170619~
			CAS*CO*45*4397.66**253*6.33~
			CAS*PR*2*79.07~
			REF*LU*24~
			REF*6R*2~
			AMT*B6*395.34~
			CLP*0022*19*9586*464.92*118.6*MB*0917321269130*24*1~
			NM1*QC*1*xxxxxxxxxx*xxxxxxxxxxxx****HN*xxxxxxxxxxx~
			NM1*TT*2*HORIZON BCBS OF NEW JERSEY*****PI*30013~
			MOA***MA01*MA18~
			DTM*050*20171117~
			SVC*HC:64483:RT*4793*309.94**1~
			DTM*472*20170619~
			CAS*CO*45*4397.66**253*6.33~
			CAS*PR*2*79.07~
			REF*LU*24~
			REF*6R*1~
			AMT*B6*395.34~
			SVC*HC:64483:LT:51*4793*154.98**1*HC:64483:LT~
			DTM*472*20170619~
			CAS*CO*45*4397.66**253*3.16**59*197.67~
			CAS*PR*2*39.53~
			REF*LU*24~
			REF*6R*2~
			AMT*B6*197.67~
			CLP*0023*19*4793*309.94*79.07*MB*0917321269160*24*1~
			NM1*QC*1*xxxxxxxx*xxxxxxx*W***HN*xxxxxxxxxxxxxxxx~
			NM1*TT*2*CHCS SERVICES INC*****PI*80255~
			MOA***MA01*MA18~
			DTM*050*20171117~
			SVC*HC:64483*4793*309.94**1~
			DTM*472*20170824~
			CAS*CO*45*4397.66**253*6.33~
			CAS*PR*2*79.07~
			REF*LU*24~
			REF*6R*1~
			AMT*B6*395.34~
			CLP*0024*19*9586*464.92*118.6*MB*0917321269120*24*1~
			NM1*QC*1*xxxxxxx*xxxxxx****HN*xxxxxxxxxxxx~
			NM1*TT*2*HORIZON BCBS OF NEW JERSEY*****PI*00019~
			MOA***MA01*MA18~
			DTM*050*20171117~
			SVC*HC:64483:RT*4793*309.94**1~
			DTM*472*20170619~
			CAS*CO*45*4397.66**253*6.33~
			CAS*PR*2*79.07~
			REF*LU*24~
			REF*6R*1~
			AMT*B6*395.34~
			SVC*HC:64483:LT:51*4793*154.98**1*HC:64483:LT~
			DTM*472*20170619~
			CAS*CO*45*4397.66**253*3.16**59*197.67~
			CAS*PR*2*39.53~
			REF*LU*24~
			REF*6R*2~
			AMT*B6*197.67~
			SE*116*000000002~
			GE*2*290356345~
			IEA*1*290356345~
		";

		$file = new \OpakeAdmin\Service\Navicure\Claims\IncomingFiles\E835ClaimPayment($text);

		$handler = new \OpakeAdmin\Service\Navicure\Claims\ResponseHandler();
		$handler->handleSingleIncomingFile($file);
	}

	public function restoreMissedNavicure835($logIds)
	{
		$logIds = explode(',', $logIds);
		foreach ($logIds as $logId) {
			$logId = trim($logId);
			print "Updating log record #" . $logId . "...\r\n";
			$logModel = $this->pixie->orm->get('Billing_Navicure_Log', $logId);
			if (!$logModel->loaded()) {
				throw new \Exception('Log model is not found');
			}

			$file = new \OpakeAdmin\Service\Navicure\Claims\IncomingFiles\E835ClaimPayment($logModel->data);
			$file->setSkipFirstBunch(true);

			$handler = new \OpakeAdmin\Service\Navicure\Claims\ResponseHandler();
			$handler->handleSingleIncomingFile($file);

			echo "Done\r\n";
		}
	}

	public function collectPayerInfoFromInsurance()
	{
		$cmd = new \Console\Command\CollectPayerInfoFromInsurance;
		$cmd->run();
	}
}
