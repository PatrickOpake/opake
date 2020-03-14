<?php

use \Console\Migration\BaseMigration;

class PatientContactInfo extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `patient`
                ADD COLUMN `home_address` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `home_apt_number` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `home_state_id` INT(11) NULL DEFAULT NULL,
                ADD COLUMN `home_city_id` INT(11) NULL DEFAULT NULL,
                ADD COLUMN `home_zip_code` VARCHAR(20) NULL DEFAULT NULL,
                ADD COLUMN `home_country_id` INT(11) NULL DEFAULT NULL,
                ADD COLUMN `home_phone` VARCHAR(40) NULL DEFAULT NULL,
                ADD COLUMN `home_phone_cell` VARCHAR(40) NULL DEFAULT NULL,
                ADD COLUMN `home_email` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `mailing_address` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `mailing_apt_number` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `mailing_city_id` INT(11) NULL DEFAULT NULL,
                ADD COLUMN `mailing_state_id` INT(11) NULL DEFAULT NULL,
                ADD COLUMN `mailing_zip_code` VARCHAR(20) NULL DEFAULT NULL,
                ADD COLUMN `mailing_country_id` INT(11) NULL DEFAULT NULL,
                ADD COLUMN `ec_name` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `ec_relationship` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `ec_phone_number` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `kin_name` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `kin_phone` VARCHAR(40) NULL DEFAULT NULL,
                ADD COLUMN `kin_address` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `kin_apt_number` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `kin_city_id` INT(11) NULL DEFAULT NULL,
                ADD COLUMN `kin_state_id` INT(11) NULL DEFAULT NULL,
                ADD COLUMN `kin_zip_code` VARCHAR(20) NULL DEFAULT NULL,
                ADD COLUMN `kin_country_id` INT(11) NULL DEFAULT NULL;
        ");

		$q = $this->getDb()->query('select')->table('patient')
			->fields('id', 'address1', 'address2', 'zip_code', 'phone_home',
				'phone_cell', 'email', 'emergency_contact',
				'relationship_to_patient', 'country_id', 'state_id', 'city_id')
			->execute();

		foreach ($q as $row) {
			$row = (array)$row;
			$address = $row['address1'];
			if (!empty($row['address2'])) {
				$address .= ' ' . $row['address2'];
			}
			$this->getDb()->query('update')->table('patient')
				->data([
					'home_address' => $address,
					'home_state_id' => $row['state_id'],
					'home_city_id' => $row['city_id'],
					'home_country_id' => $row['country_id'],
					'home_zip_code' => $row['zip_code'],
					'home_phone' => $row['phone_home'],
					'home_phone_cell' => $row['phone_cell'],
					'home_email' => $row['email'],
					'ec_name' => $row['emergency_contact'],
					'ec_relationship' => $row['relationship_to_patient']
				])
				->where('id', $row['id'])
				->execute();
		}
	}
}
