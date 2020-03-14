<?php

use \Console\Migration\BaseMigration;

class UpdateInsurances extends BaseMigration
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
            ALTER TABLE `patient_insurance` CHANGE `insurance_id` `insurance_id` INT(11) NULL;
            ALTER TABLE `case_registration_insurance` CHANGE `insurance_id` `insurance_id` INT(11) NULL;
            ALTER TABLE `patient`
              CHANGE `ssn` `ssn` VARCHAR(40) NULL,
              CHANGE `gender` `gender` TINYINT NULL;

        ");
	}
}
