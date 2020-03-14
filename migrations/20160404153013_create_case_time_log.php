<?php

use \Console\Migration\BaseMigration;

class CreateCaseTimeLog extends BaseMigration
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
            CREATE TABLE IF NOT EXISTS `case_time_log` (
                `id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL,
                `stage` VARCHAR (255) NULL,
                `time` TIME NULL,
                `time_mode` VARCHAR(2) NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            ALTER TABLE `case_time_log` ADD PRIMARY KEY (`id`);
            ALTER TABLE `case_time_log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

             CREATE TABLE IF NOT EXISTS `case_time_log_staff` (
                    `timelog_id` int(11) NOT NULL,
                    `staff_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_time_log_staff`
                    ADD UNIQUE KEY `uni` (`timelog_id`,`staff_id`);
        ");
    }
}
