<?php

use \Console\Migration\BaseMigration;

class AddFieldsForClaim extends BaseMigration
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
          ALTER TABLE `organization` ADD `federal_tax` VARCHAR(255) NULL;
          ALTER TABLE `case_claim` ADD `federal_tax` VARCHAR(255) NULL;
          ALTER TABLE `case_registration` ADD `admission_hour` INT(11) NULL;
          ALTER TABLE `organization` ADD `npi` VARCHAR(10) NULL;
          ALTER TABLE `case_claim` ADD `npi` VARCHAR(10) NULL;
          ALTER TABLE `site` ADD `city_id` INT(11) NULL;
          ALTER TABLE `site` ADD `state_id` INT(11) NULL;
          ALTER TABLE `site` ADD `zip_code` VARCHAR (20) NULL;
        ");
	}
}
