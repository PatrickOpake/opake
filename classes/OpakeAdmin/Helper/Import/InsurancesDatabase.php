<?php

namespace OpakeAdmin\Helper\Import;

use Opake\Helper\TimeFormat;
use OpakeAdmin\Helper\Export\InsurancesDatabaseExport;

class InsurancesDatabase extends AbstractImport
{

	public function load($filename)
	{
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 600);

		$phpExcel = $this->readFromExcel($filename);

		$subject = $phpExcel->getProperties()->getSubject();

		if ($subject && $subject !== InsurancesDatabaseExport::getCurrentExportSubject()) {
			throw new \Exception('The file format is outdated');
		}

		$sheet = $phpExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();

		$startRowNumber = 4;
		$insurancesArray = [];

		for ($i = $startRowNumber; $i <= $highestRow; ++$i) {

			$insuranceCompany = trim($sheet->getCell('A' . $i)->getValue());
			$ub04PayerId = trim($sheet->getCell('B' . $i)->getValue());
			$cms1500PayerId = trim($sheet->getCell('C' . $i)->getValue());
			$navicureEligibilityId = trim($sheet->getCell('D' . $i)->getValue());
			$insuranceCardPayerId = trim($sheet->getCell('E' . $i)->getValue());
			$insuranceType = trim($sheet->getCell('F' . $i)->getValue());
			$lastEditedDate = trim($sheet->getCell('G' . $i)->getValue());
			$userName = trim($sheet->getCell('H' . $i)->getValue());

			$model = $this->pixie->orm->get('Insurance_Payor')
				->where('name', $insuranceCompany)
				->order_by('id', 'desc')
				->limit(1)
				->find();


			$insurancePayorId = null;
			if ($model->loaded()) {
				$insurancePayorId = $model->id();
			}

			$usedUserId = null;

			if($userName) {
				$usedUserRow = $this->pixie->db->query('select')
					->table('user')
					->fields('id')
					->where($this->pixie->db->expr("CONCAT_WS(' ',first_name,last_name)"), 'like', '%' . $userName . '%')
					->execute()
					->current();
				if ($usedUserRow) {
					$usedUserId = $usedUserRow->id;
				}
			}

			$addressesResult = [];
			$addresses = $sheet->rangeToArray('I' . $i . ':CE' . $i);
			$addresses = $addresses[0];
			$addresses = array_chunk($addresses, 5);

			foreach ($addresses as $addressGroup) {

				$address = $addressGroup[0];
				$city = $addressGroup[1];
				$state = $addressGroup[2];
				$zipCode = $addressGroup[3];
				$phone = $addressGroup[4];

				if ($address || $city || $state || $zipCode || $phone) {

					$usedCityId = null;
					$usedStateId = null;

					if ($state) {
						$usedStateRow = $this->pixie->db->query('select')
							->table('geo_state')
							->fields('id')
							->where('code', $state)
							->execute()
							->current();

						if ($usedStateRow) {
							$usedStateId = $usedStateRow->id;

							if ($city) {
								$usedCityRow = $this->pixie->db->query('select')
									->table('geo_city')
									->fields('id')
									->where('name', $city)
									->where('state_id', $usedStateId)
									->execute()
									->current();

								if ($usedCityRow) {
									$usedCityId = $usedCityRow->id;
								} else {

									if ($city === strtoupper($city)) {
										$city = ucwords(strtolower($city));
									}

									$newCity = $this->pixie->orm->get('Geo_City');
									$newCity->state_id = $usedStateId;
									$newCity->name = $city;

									$newCity->save();

									$usedCityId = $newCity->id();
								}
							}

						}
					}

					$addressesResult[] = [
						'address' => $address,
					    'city_id' => $usedCityId,
					    'state_id' => $usedStateId,
					    'zip_code' => $zipCode,
					    'phone' => $phone
					];

				} else {
					$addressesResult[] = [
						'empty' => true
					];
				}
			}

			if ($insuranceType === '' || $insuranceType === null) {
				$insuranceType = null;
			}

			$insurancesArray[] = [
				'id' => $insurancePayorId,
				'name' => $insuranceCompany,
				'ub04_payer_id' => $ub04PayerId,
				'cms1500_payer_id' => $cms1500PayerId,
				'navicure_eligibility_payor_id' => $navicureEligibilityId,
				'carrier_code' => $insuranceCardPayerId,
				'insurance_type' => $insuranceType,
				'last_change_date' => $lastEditedDate ? TimeFormat::formatToDBDatetime($lastEditedDate) : null,
				'last_change_user_id' => $usedUserId,
			    'addresses' => $addressesResult
			];

		}

		$phpExcel = null;
		$sheet = null;

		$db = $this->pixie->db;
		$db->begin_transaction();

		try {
			$firstItemUploaded = false;
			foreach ($insurancesArray as $payor) {
				if ($payor['name']) {

					$data = [
						'name' => $payor['name'],
						'ub04_payer_id' => $payor['ub04_payer_id'],
						'cms1500_payer_id' => $payor['cms1500_payer_id'],
						'navicure_eligibility_payor_id' => $payor['navicure_eligibility_payor_id'],
						'carrier_code' => $payor['carrier_code'],
						'insurance_type' => $payor['insurance_type'],
						'last_change_date' => $payor['last_change_date'],
						'last_change_user_id' => $payor['last_change_user_id'],
					];

					if ($payor['id']) {

						$this->pixie->db->query('update')
							->table('insurance_payor')
							->data($data)
							->where('id', $payor['id'])
							->execute();

					} else {

						$this->pixie->db->query('insert')
							->table('insurance_payor')
							->data($data)
							->execute();

						$payor['id'] = $this->pixie->db->get()->insert_id();

					}

					$addresses = $payor['addresses'];
					$modelAddresses = $this->pixie->db->query('select')
						->table('insurance_payor_address')
						->fields('id')
						->where('payor_id', $payor['id'])
						->order_by('id')
						->limit(15)
						->execute();

					$i = 0;
					foreach ($modelAddresses as $row) {
						$addresses[$i]['id'] = $row->id;
						++$i;
					}

					foreach ($addresses as $addressData) {
						if (!empty($addressData['empty'])) {
							if (!empty($addressData['id'])) {
								$this->pixie->db->query('delete')
									->table('insurance_payor_address')
									->where('id', $addressData['id'])
									->limit(1)
									->execute();
							}
						} else {
							$dataToUpdate = [
								'payor_id' => $payor['id'],
								'address' => $addressData['address'],
							    'city_id' => $addressData['city_id'],
							    'state_id' => $addressData['state_id'],
							    'zip_code' => $addressData['zip_code'],
							    'phone' => $addressData['phone']
							];
							if (!empty($addressData['id'])) {
								$this->pixie->db->query('update')
									->table('insurance_payor_address')
									->data($dataToUpdate)
									->where('id', $addressData['id'])
									->execute();
							} else {
								$this->pixie->db->query('insert')
									->table('insurance_payor_address')
									->data($dataToUpdate)
									->execute();
							}
						}
					}

					$firstItemUploaded = true;

				}  else {
					if (!$firstItemUploaded) {
						throw new \Exception('Insurance company must be filled');
					}
				}


			}
		} catch (\Exception $e) {
			$db->rollback();
			throw $e;
		}

		$db->commit();
	}

	public static function getAllowedMimeTypes()
	{
		return [
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'text/csv',
		    'text/plain'
		];
	}
}