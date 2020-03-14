<?php

use Phinx\Migration\AbstractMigration;

class RegistrationInsuranceAddInsurance extends AbstractMigration
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
		$this->query('
			ALTER TABLE `case_registration_insurance` ADD `insurance_id` INT(11) NOT NULL AFTER `registration_id`;
			ALTER TABLE `case_registration_insurance` DROP `carrier`, DROP `address`, DROP `state_id`, DROP `zip_code`, DROP `city_id`, DROP `country_id`;
		');
	}
}
