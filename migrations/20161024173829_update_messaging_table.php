<?php

use \Console\Migration\BaseMigration;

class UpdateMessagingTable extends BaseMigration
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
		ALTER TABLE `messaging` DROP INDEX `IDX_send_date`;
		ALTER TABLE `messaging` DROP `organization_id`;		
		ALTER TABLE `messaging` ADD INDEX `IDX_sender_id_recipient_id` (`sender_id`, `recipient_id`);
	");
    }
}
