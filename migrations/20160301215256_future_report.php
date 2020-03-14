<?php

use \Console\Migration\BaseMigration;

class FutureReport extends BaseMigration
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
            ALTER TABLE `case_op_report_future`
              ADD COLUMN `specimens_removed` TEXT NULL,
              ADD COLUMN `findings` TEXT NULL,
              ADD COLUMN `urine_output` VARCHAR(255) NULL,
              ADD COLUMN `fluids` VARCHAR(255) NULL,
              ADD COLUMN `blood_transfused` VARCHAR(255) NULL,
              ADD COLUMN `total_tourniquet_time` VARCHAR(255) NULL,
              ADD COLUMN `clinical_history` VARCHAR(255) NULL;
        ");
	}
}
