<?php

use \Console\Migration\BaseMigration;

class SmsTemplate extends BaseMigration
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
		CREATE TABLE IF NOT EXISTS `sms_template` (
                    `id` int(11) NOT NULL,
                    `organization_id` int(11) NOT NULL,
                    `reminder_sms` tinyint(1) NULL DEFAULT '0',
                    `hours_before` tinyint(2) NULL,
                    `schedule_msg` TEXT NULL,
                    `poc_sms` tinyint(1) NULL DEFAULT '0',
                    `poc_msg` TEXT NULL,
                    `acc_sid` VARCHAR (255) NULL,
                    `auth_token` VARCHAR (255) NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `sms_template` ADD PRIMARY KEY (`id`);
                ALTER TABLE `sms_template` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
    	");
    }
}
