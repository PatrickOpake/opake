<?php

use \Console\Migration\BaseMigration;

class AddPayorFields extends BaseMigration
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
		ALTER TABLE `insurance_payor` ADD `ub04_payer_id` VARCHAR(255)  NULL  DEFAULT NULL;
		ALTER TABLE `insurance_payor` ADD `cms1500_payer_id` VARCHAR(255)  NULL  DEFAULT NULL;
	");
    }
}
