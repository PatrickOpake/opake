<?php

use \Console\Migration\BaseMigration;

class AddElectronicSignatureToOpReport extends BaseMigration
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
            ALTER TABLE `case_op_report` ADD COLUMN `time_submitted` DATETIME NULL DEFAULT NULL AFTER `time_start`;
            ALTER TABLE `case_op_report` ADD COLUMN `time_signed` DATETIME NULL DEFAULT NULL AFTER `time_submitted`;
            ALTER TABLE `case_op_report` ADD COLUMN `signed_user_id` INT(11) NULL DEFAULT NULL AFTER `time_signed`;
            
            CREATE TABLE IF NOT EXISTS `case_op_report_amendment` (
                    `id` int(11) NOT NULL,
                    `report_id` int(11) NOT NULL,
                    `time_signed` DATETIME NOT NULL,
                    `user_signed` int(11) NOT NULL,
                    `text` TEXT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `case_op_report_amendment` ADD PRIMARY KEY (`id`);
                ALTER TABLE `case_op_report_amendment` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");

        $this->query("
            CREATE TABLE IF NOT EXISTS `operative_report_note` (
                `id` int(11) NOT NULL,
                `report_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `time_add` timestamp NULL DEFAULT NULL,
                `text` TEXT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `operative_report_note` ADD PRIMARY KEY (`id`);
            ALTER TABLE `operative_report_note` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            
            ALTER TABLE `case_op_report` ADD COLUMN `notes_count` INT(11) NOT NULL DEFAULT 0;
        ");

        $this->query("
            CREATE TABLE IF NOT EXISTS `user_operative_report_note` (
                `id` int(11) NOT NULL,
                `report_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `last_read_note_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `user_operative_report_note` ADD PRIMARY KEY (`id`);
            ALTER TABLE `user_operative_report_note` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
    }
}
