<?php

use \Console\Migration\BaseMigration;

class NavicureUpdatePayorData extends BaseMigration
{
    public function change()
    {
		$app = $this->getApp();

	    $this->query("ALTER TABLE `insurance_payor` ADD `is_medicare` TINYINT(1)  NULL  DEFAULT '0'  AFTER `zip_code`;");
	    $this->query("ALTER TABLE `insurance_payor` ADD `is_claims_enrollment_required` TINYINT(1)  NULL  DEFAULT '0'  AFTER `zip_code`;");
	    $this->query("ALTER TABLE `insurance_payor` ADD `is_electronic_secondary` TINYINT(1)  NULL  DEFAULT '0'  AFTER `zip_code`;");
	    $this->query("ALTER TABLE `insurance_payor` ADD `navicure_payor_id` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `zip_code`;");
	    $this->query("ALTER TABLE `insurance_payor` ADD `navicure_eligibility_payor_id` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `navicure_payor_id`;");
	    $this->query("ALTER TABLE `insurance_payor` ADD `era_payor_code` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `navicure_eligibility_payor_id`;");

	    try {

		    $this->getDb()->begin_transaction();

		    $inputFile = realpath(__DIR__) . '/../_tmp/Navicure_FullPayerList_Addresses.xlsx';
		    $startRow = 2;

		    $inputFileType = PHPExcel_IOFactory::identify($inputFile);
		    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
		    $objPHPExcel = $objReader->load($inputFile);

		    $sheet = $objPHPExcel->getActiveSheet();
		    $highestRow = $sheet->getHighestRow();

		    $this->getDb()->query('update')
			    ->table('insurance_payor')
			    ->data([
				    'actual' => 0
			    ])
			    ->where('is_remote_payor', 1)
		        ->execute();

		    for ($i = $startRow; $i <= $highestRow; $i++) {
			    $companyName = trim($sheet->getCell('A' . $i)->getValue());
			    $code = trim($sheet->getCell('B' . $i)->getValue());
			    $eraPayerCode = trim($sheet->getCell('C' . $i)->getValue());
			    $eligibilityCode = trim($sheet->getCell('D' . $i)->getValue());
			    $claimsEnrollmentRequired = trim($sheet->getCell('E' . $i)->getValue());
			    $electronicSecondary = trim($sheet->getCell('F' . $i)->getValue());
			    $address = trim($sheet->getCell('G' . $i)->getValue());
			    $city = trim($sheet->getCell('H' . $i)->getValue());
			    $state = trim($sheet->getCell('I' . $i)->getValue());
			    $zip = trim($sheet->getCell('J' . $i)->getValue());

			    $usedCityId = null;
			    $usedStateId = null;

			    if ($state) {
				    $usedStateRow = $this->getDb()->query('select')
					    ->table('geo_state')
					    ->fields('id')
					    ->where('code', $state)
					    ->execute()
					    ->current();

				    if ($usedStateRow) {
					    $usedStateId = $usedStateRow->id;

					    if ($city) {
						    $usedCityRow = $this->getDb()->query('select')
							    ->table('geo_city')
							    ->fields('id')
							    ->where('name', $city)
							    ->where('state_id', $usedStateId)
							    ->execute()
							    ->current();

						    if ($usedCityRow) {
							    $usedCityId = $usedCityRow->id;
						    } else {
							    print "City " . $city . " / " . $state . " is not found\r\n";

							    if ($city === strtoupper($city)) {
								    $city = ucwords(strtolower($city));
							    }

							    $newCity = $app->orm->get('Geo_City');
							    $newCity->state_id = $usedStateId;
							    $newCity->name = $city;

							    $newCity->save();

							    $usedCityId = $newCity->id();
						    }
					    }

				    } else {
					    print "State for code " . $state . " is not found\r\n";
				    }
			    }

			    $record = $app->orm->get('Insurance_Payor')
			        ->where('name', $companyName)
				    ->where('is_remote_payor', 1)
				    ->find();

			    if (!$record->loaded()) {
				    $record = $app->orm->get('Insurance_Payor');
			    }

			    $record->name = $companyName;
			    $record->is_remote_payor = 1;
			    $record->actual = 1;
				$record->navicure_payor_id = ($code) ? : null;
			    $record->navicure_eligibility_payor_id = ($eligibilityCode) ? : null;
			    $record->era_payor_code = ($eraPayerCode) ? : null;
			    $record->is_claims_enrollment_required = ($claimsEnrollmentRequired === 'Enrollment Required') ? 1 : 0;
			    $record->is_electronic_secondary = ($electronicSecondary === 'Yes') ? 1 : 0;
				$record->address = $address;
			    $record->city_id = $usedCityId;
			    $record->state_id = $usedStateId;
			    $record->zip_code = $zip;

			    $medicareString = 'MEDICARE';
			    if (substr($companyName, 0, strlen($medicareString)) === $medicareString) {
				    $record->is_medicare = 1;
			    }

			    $record->save();
		    }

		    $this->getDb()->commit();

	    } catch (\Exception $e) {
		    $this->getDb()->rollback();
		    throw $e;
	    }

    }
}
