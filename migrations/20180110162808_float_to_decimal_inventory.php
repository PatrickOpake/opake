<?php

use \Console\Migration\BaseMigration;

class FloatToDecimalInventory extends BaseMigration
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
		ALTER TABLE `inventory` CHANGE `unit_price` `unit_price` DECIMAL(12,2)  NULL  DEFAULT NULL;
		ALTER TABLE `inventory` CHANGE `charge_amount` `charge_amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
		ALTER TABLE `inventory` CHANGE `unit_weight` `unit_weight` DECIMAL(12,2)  NULL  DEFAULT NULL;
	");
    }
}
