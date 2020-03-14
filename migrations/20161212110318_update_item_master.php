<?php

use \Console\Migration\BaseMigration;

class UpdateItemMaster extends BaseMigration
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
		ALTER TABLE `inventory` ADD COLUMN `is_implantable` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_generic`;
		ALTER TABLE `inventory` ADD COLUMN `is_latex` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_implantable`;
		ALTER TABLE `inventory` ADD COLUMN `is_hazardous` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_latex`;
		ALTER TABLE `inventory` ADD COLUMN `hims_indicator` VARCHAR(20) NULL DEFAULT NULL AFTER `is_hazardous`;
		ALTER TABLE `inventory` ADD COLUMN `unspsc` VARCHAR(20) NULL DEFAULT NULL AFTER `hims_indicator`;
	");
    }
}
