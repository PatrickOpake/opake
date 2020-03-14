<?php

use \Console\Migration\BaseMigration;

class DropPatientField extends BaseMigration
{
	/**
	 * Change Method.
	 *
	 * Write your reversible migrations using this method.
	 *
	 * More information on writing migrations is available here:
	 * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
	 *
	 * The following commands can be used in this method and Phinx will
	 * automatically reverse them when rolling back:
	 *
	 *    createTable
	 *    renameTable
	 *    addColumn
	 *    renameColumn
	 *    addIndex
	 *    addForeignKey
	 *
	 * Remember to call "create()" or "update()" and NOT "save()" when working
	 * with the Table class.
	 */
	public function change()
	{
		$this->query("
        ALTER TABLE `patient` DROP `kin_name`, DROP `kin_phone`, DROP `kin_address`, DROP `kin_apt_number`, DROP `kin_city_id`, DROP `kin_state_id`, DROP `kin_zip_code`, DROP `kin_country_id`;
        ALTER TABLE `patient` DROP `mailing_same_as_home`, DROP `mailing_address`, DROP `mailing_apt_number`, DROP `mailing_city_id`, DROP `mailing_state_id`, DROP `mailing_zip_code`, DROP `mailing_country_id`;
        ALTER TABLE `patient` DROP `school`;
        ALTER TABLE `patient` ADD `employer_phone` VARCHAR(40) NULL;
        ALTER TABLE `case_registration` DROP `home_phone_cell`, DROP `relationship_to_insured`, DROP `specific_title`, DROP `specific_first_name`, DROP `specific_middle_name`, DROP `specific_last_name`, DROP `specific_suffix`,
                    DROP `specific_gender`, DROP `specific_dob`, DROP `specific_address`, DROP `specific_city_id`, DROP `specific_country_id`, DROP `specific_apt_number`, DROP `specific_state_id`, DROP `specific_zip_code`, DROP `specific_phone`,
                    DROP `medical_history`, DROP `admission_hour`, DROP `accompanied`, DROP `mobility`;
        ALTER TABLE `patient` DROP `home_phone_cell`;
        ");
	}
}
