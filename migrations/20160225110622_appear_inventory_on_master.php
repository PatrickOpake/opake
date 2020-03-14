<?php

use \Console\Migration\BaseMigration;

class AppearInventoryOnMaster extends BaseMigration
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
          ALTER TABLE `inventory` CHANGE `barcode` `item_number` VARCHAR(40) NULL DEFAULT NULL;
          ALTER TABLE `inventory` CHANGE `price` `unit_price` FLOAT NULL DEFAULT NULL;
          ALTER TABLE `master_inventory`
            ADD COLUMN `type` varchar(20) NULL DEFAULT NULL,
            ADD COLUMN `is_remanufacturable` tinyint(1) NULL DEFAULT '0',
            ADD COLUMN `is_resterilizable` tinyint(1) NULL DEFAULT '0',
            ADD COLUMN `is_reusable` tinyint(1) NULL DEFAULT '0',
            ADD COLUMN `is_generic` tinyint(1) NULL DEFAULT '0';

          CREATE TABLE IF NOT EXISTS `master_items_substitutes` (
              `id` int(11) NOT NULL,
              `item_id` int(11) NOT NULL,
              `substitute_id` int(11) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
          ALTER TABLE `master_items_substitutes`
          ADD PRIMARY KEY (`id`),
          ADD KEY `item_id` (`item_id`),
          ADD KEY `substitute_id` (`substitute_id`);
          ALTER TABLE `master_items_substitutes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

          CREATE TABLE IF NOT EXISTS `inventory_substitutes` (
              `id` int(11) NOT NULL,
              `item_id` int(11) NOT NULL,
              `substitute_id` int(11) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
          ALTER TABLE `inventory_substitutes`
          ADD PRIMARY KEY (`id`),
          ADD KEY `item_id` (`item_id`),
          ADD KEY `substitute_id` (`substitute_id`);
          ALTER TABLE `inventory_substitutes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

        ");
	}
}
