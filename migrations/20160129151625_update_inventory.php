<?php

use \Console\Migration\BaseMigration;

class UpdateInventory extends BaseMigration
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
        ALTER TABLE `inventory` DROP `is_kit`, DROP `barcode_type`;
        ALTER TABLE `inventory` ADD COLUMN `auto_order` TINYINT(1) NULL DEFAULT 0;
        INSERT INTO `inventory_type` (name) VALUES ('Kit');

        CREATE TABLE IF NOT EXISTS `inventory_kit_items` (
          `inventory_id` int(11) NOT NULL,
          `item_id` int(11) NOT NULL,
          `quantity` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        ");
	}
}
