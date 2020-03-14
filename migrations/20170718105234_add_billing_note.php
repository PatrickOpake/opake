<?php

use \Console\Migration\BaseMigration;

class AddBillingNote extends BaseMigration
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
	    CREATE TABLE IF NOT EXISTS `billing_note` (
                `id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `patient_id` int(11) NULL DEFAULT NULL,
                `time_add` timestamp NULL DEFAULT NULL,
                `text` TEXT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `billing_note` ADD PRIMARY KEY (`id`);
            ALTER TABLE `billing_note` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            
            ALTER TABLE `case` ADD COLUMN `billing_notes_count` INT(11) NOT NULL DEFAULT 0 AFTER `notes_count`;
            
	    CREATE TABLE IF NOT EXISTS `user_billing_note` (
                `id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `last_read_note_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `user_billing_note` ADD PRIMARY KEY (`id`);
            ALTER TABLE `user_billing_note` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
    	");
    }
}
