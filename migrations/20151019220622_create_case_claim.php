<?php

use \Console\Migration\BaseMigration;

class CreateCaseClaim extends BaseMigration
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
            CREATE TABLE IF NOT EXISTS `case_claim` (
              `id` int(11) NOT NULL,
              `case_id` int(11) NOT NULL,
              `provider_name` varchar(255) DEFAULT NULL,
              `provider_address` varchar(255) DEFAULT NULL,
              `provider_city_id` int(11) DEFAULT NULL,
              `provider_state_id` int(11) DEFAULT NULL,
              `provider_country_id` int(11) DEFAULT NULL,
              `provider_zip_code` varchar(20) DEFAULT NULL,
              `provider_phone` varchar(40) DEFAULT NULL,
              `provider_fax` varchar(40) DEFAULT NULL,
              `pay_name` varchar(255) DEFAULT NULL,
              `pay_address` varchar(255) DEFAULT NULL,
              `pay_country_id` int(11) DEFAULT NULL,
              `pay_city_id` int(11) DEFAULT NULL,
              `pay_state_id` int(11) DEFAULT NULL,
              `pay_zip_code` varchar(20) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_claim`
              ADD PRIMARY KEY (`id`);
            ALTER TABLE `case_claim`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
	}
}
