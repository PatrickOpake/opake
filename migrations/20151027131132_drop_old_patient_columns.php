<?php

use \Console\Migration\BaseMigration;

class DropOldPatientColumns extends BaseMigration
{
	public function change()
	{
		$this->query("
           ALTER TABLE `patient`
            DROP COLUMN `country_id`,
            DROP COLUMN `state_id`,
            DROP COLUMN `city_id`,
            DROP COLUMN `zip_code`,
            DROP COLUMN `address1`,
            DROP COLUMN `address2`,
            DROP COLUMN `phone`,
            DROP COLUMN `phone_home`,
            DROP COLUMN `phone_cell`,
            DROP COLUMN `email`,
            DROP COLUMN `emergency_contact`,
            DROP COLUMN `relationship_to_patient`;
        ");


	}
}
