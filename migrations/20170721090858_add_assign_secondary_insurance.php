<?php

use \Console\Migration\BaseMigration;

class AddAssignSecondaryInsurance extends BaseMigration
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
		ALTER TABLE `case_coding` 
			ADD `has_assign_to_secondary_insurer` tinyint(1) NULL DEFAULT NULL,
			ADD `amount_paid_by_primary_insurance` FLOAT NULL DEFAULT NULL;
	");
    }
}
