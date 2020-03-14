<?php

use \Console\Migration\BaseMigration;

class PatientStatement extends BaseMigration
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
		 CREATE TABLE IF NOT EXISTS `patient_statement_history` (
                    `id` int(11) NOT NULL,
                    `patient_id` int(11) NOT NULL,
                    `date_generated` DATETIME NULL DEFAULT NULL,
                    `print_result_id` int(11) NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `patient_statement_history` ADD PRIMARY KEY (`id`);
                ALTER TABLE `patient_statement_history` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
	");
    }
}
