<?php

namespace Console\Command;

use Opake\Helper\TimeFormat;

class CollectPayerInfoFromInsurance
{
	public function run()
	{
		$app = \Opake\Application::get();
		$db = $app->db;
		$db->begin_transaction();

		try {
			$this->updateRegular();
			$this->updateAutoAccident();
			$this->updateWorkersComp();

			$db->commit();
		} catch (\Exception $e) {
			$db->rollback();
			throw $e;
		}

		echo "Done\r\n";
	}

	protected function updateRegular()
	{
		$this->updateInsurances(false, false);
	}

	protected function updateAutoAccident()
	{
		$this->updateInsurances(false, true);
	}

	protected function updateWorkersComp()
	{
		$this->updateInsurances(true, false);
	}

	protected function updateInsurances($isWorkersComp = false, $isAutoAccident = false)
	{
		$app = \Opake\Application::get();
		$db = $app->db;
		$currentDateDbFormat = TimeFormat::formatToDBDatetime(new \DateTime());

		if ($isWorkersComp) {
			$insuranceDataTableName = 'insurance_data_workers_comp';
		} else if ($isAutoAccident) {
			$insuranceDataTableName = 'insurance_data_auto_accident';
		} else {
			$insuranceDataTableName = 'insurance_data_regular';
		}

		if ($isWorkersComp || $isAutoAccident) {
			$rows = $db->query('select')
				->table($insuranceDataTableName)
				->fields( $insuranceDataTableName . '.*')
				->where('insurance_name', 'IS NOT NULL', $db->expr(''))
				->where('insurance_company_id', 'IS NULL', $db->expr(''))
				->execute();
		} else {
			$rows = $db->query('select')
				->table($insuranceDataTableName)
				->fields( $insuranceDataTableName . '.*')
				->where('insurance_company_name', 'IS NOT NULL', $db->expr(''))
				->where('insurance_id', 'IS NULL', $db->expr(''))
				->execute();
		}

		foreach ($rows as $row) {
			if ($isWorkersComp || $isAutoAccident) {
				$insuranceCompanyName = trim($row->insurance_name);
			} else {
				$insuranceCompanyName = trim($row->insurance_company_name);
			}

			if ($insuranceCompanyName) {
				$payerRow = $db->query('select')
					->fields('id')
					->table('insurance_payor')
					->where('name', $insuranceCompanyName)
					->limit(1)
					->execute()
					->current();

				//update existing
				if ($payerRow) {

					print "Found company '" . $insuranceCompanyName . "', set it to insurance...\r\n";
					$payerId = $payerRow->id;
					$payerAddressId = null;

					if ($isAutoAccident || $isWorkersComp) {
						$addressRowData = [
							'payor_id' => $payerId,
							'address' => $row->insurance_address,
							'phone' => $row->insurance_company_phone,
							'state_id' => $row->state_id,
							'city_id' => $row->city_id,
							'zip_code' => $row->zip
						];
					} else {
						$addressRowData = [
							'payor_id' => $payerId,
							'address' => $row->address_insurance,
							'phone' => $row->provider_phone,
							'state_id' => $row->insurance_state_id,
							'city_id' => $row->insurance_city_id,
							'zip_code' => $row->insurance_zip_code
						];
					}

					if ($addressRowData['address'] || $addressRowData['phone'] ||
						$addressRowData['state_id'] || $addressRowData['city_id'] ||
						$addressRowData['zip_code']) {

						$q = $db->query('select')
							->table('insurance_payor_address')
							->fields('id')
							->where('payor_id', $addressRowData['payor_id']);
						if ($addressRowData['address'] === null) {
							$q->where('address', 'IS NULL', $db->expr(''));
						} else {
							$q->where('address', $addressRowData['address']);
						}
						if ($addressRowData['phone'] === null) {
							$q->where('phone', 'IS NULL', $db->expr(''));
						} else {
							$q->where('phone', $addressRowData['phone']);
						}
						if ($addressRowData['state_id'] === null) {
							$q->where('state_id', 'IS NULL', $db->expr(''));
						} else {
							$q->where('state_id', $addressRowData['state_id']);
						}
						if ($addressRowData['city_id'] === null) {
							$q->where('city_id', 'IS NULL', $db->expr(''));
						} else {
							$q->where('city_id', $addressRowData['city_id']);
						}
						if ($addressRowData['zip_code'] === null) {
							$q->where('zip_code', 'IS NULL', $db->expr(''));
						} else {
							$q->where('zip_code', $addressRowData['zip_code']);
						}
						$q->limit(1);
						$addressRow = $q->execute()->current();

						if ($addressRow) {
							$payerAddressId = $addressRow->id;
							print "Found already existed address entry\r\n";
						} else {
							$db->query('insert')
								->table('insurance_payor_address')
								->data($addressRowData)
								->execute();
							$payerAddressId = $db->insert_id();
							print "Create new address entry\r\n";
						}

						print "Address data\r\n";
						print_r($addressRowData);

					} else {
						print "Address data is empty, skipped\r\n";
					}

					if ($isAutoAccident || $isWorkersComp) {
						$db->query('update')
							->table($insuranceDataTableName)
							->where('id', $row->id)
							->limit(1)
							->data([
								'insurance_company_id' => $payerId,
								'selected_insurance_company_address_id' => $payerAddressId
							])
							->execute();
					} else {
						$db->query('update')
							->table($insuranceDataTableName)
							->where('id', $row->id)
							->limit(1)
							->data([
								'insurance_id' => $payerId,
								'selected_insurance_address_id' => $payerAddressId
							])
							->execute();
					}

					continue;
				}

				//create a new one
				$payorRowData = [
					'name' => $insuranceCompanyName,
					'actual' => 1,
					'navicure_eligibility_payor_id' => $row->eligibility_payer_id,
					'ub04_payer_id' => $row->ub04_payer_id,
					'cms1500_payer_id' => $row->cms1500_payer_id,
					'last_change_date' => $currentDateDbFormat
				];

				$db->query('insert')
					->table('insurance_payor')
					->data($payorRowData)
					->execute();

				$payerId = $db->insert_id();
				$payerAddressId = null;

				print "Creating new company " . $insuranceCompanyName . "\r\n";
				print_r($payorRowData);

				if ($isAutoAccident || $isWorkersComp) {
					$addressRowData = [
						'payor_id' => $payerId,
						'address' => $row->insurance_address,
						'phone' => $row->insurance_company_phone,
						'state_id' => $row->state_id,
						'city_id' => $row->city_id,
						'zip_code' => $row->zip
					];
				} else {
					$addressRowData = [
						'payor_id' => $payerId,
						'address' => $row->address_insurance,
						'phone' => $row->provider_phone,
						'state_id' => $row->insurance_state_id,
						'city_id' => $row->insurance_city_id,
						'zip_code' => $row->insurance_zip_code
					];
				}

				if ($addressRowData['address'] || $addressRowData['phone'] ||
					$addressRowData['state_id'] || $addressRowData['city_id'] ||
					$addressRowData['zip_code']) {

					$db->query('insert')
						->table('insurance_payor_address')
						->data($addressRowData)
						->execute();

					$payerAddressId = $db->insert_id();

					print_r($addressRowData);

				} else {
					print "Address data is empty, skipped\r\n";
				}

				if ($isAutoAccident || $isWorkersComp) {
					$db->query('update')
						->table($insuranceDataTableName)
						->where('id', $row->id)
						->limit(1)
						->data([
							'insurance_company_id' => $payerId,
							'selected_insurance_company_address_id' => $payerAddressId
						])
						->execute();
				} else {
					$db->query('update')
						->table($insuranceDataTableName)
						->where('id', $row->id)
						->limit(1)
						->data([
							'insurance_id' => $payerId,
							'selected_insurance_address_id' => $payerAddressId
						])
						->execute();
				}
			}
		}
	}
}