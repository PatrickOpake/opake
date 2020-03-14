<?php

use \Console\Migration\BaseMigration;

class AddPaySectionToOrg extends BaseMigration
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
          ALTER TABLE `organization` ADD `pay_name` VARCHAR(255) NULL AFTER `contact_phone`, ADD `pay_address` VARCHAR(255) NULL AFTER `pay_name`, ADD `pay_city_id` INT(11) NULL AFTER `pay_address`, ADD `pay_state_id` INT(11) NULL AFTER `pay_city_id`, ADD `pay_zip_code` VARCHAR(20) NULL AFTER `pay_state_id`;
          ALTER TABLE `organization` ADD `pay_country_id` INT(11) NULL AFTER `pay_address`;
          ALTER TABLE `organization` ADD `contact_fax` VARCHAR(45) NULL AFTER `contact_phone`;
        ");
	}
}
