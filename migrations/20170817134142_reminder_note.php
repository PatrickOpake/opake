<?php

use \Console\Migration\BaseMigration;

class ReminderNote extends BaseMigration
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
	CREATE TABLE IF NOT EXISTS `reminder_note` (
                `id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `is_completed` tinyint(1)  NOT NULL DEFAULT '0',
                `reminder_date` DATETIME NULL DEFAULT NULL,
                `note_type` tinyint(4) DEFAULT NULL,
                `note_id` int(11) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `reminder_note` ADD PRIMARY KEY (`id`);
            ALTER TABLE `reminder_note` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
	");
    }
}
