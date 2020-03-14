<?php

use \Console\Migration\BaseMigration;

class InsuranceOrder extends BaseMigration
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
			ALTER TABLE `case_coding` ADD `insurance_order` tinyint(1) NULL DEFAULT NULL;
			ALTER TABLE `case_coding` CHANGE COLUMN `amount_paid_by_primary_insurance` `amount_paid_by_other_insurance` FLOAT NULL DEFAULT NULL;
	");
    }
}
