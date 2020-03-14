<?php

use \Console\Migration\BaseMigration;

class DirectMessaging extends BaseMigration
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

		CREATE TABLE IF NOT EXISTS `messaging` (
		  `id` int(11) NOT NULL,
		  `organization_id` int(11) NOT NULL,
		  `sender_id` int(11) NOT NULL,
		  `recipient_id` int(11) NOT NULL,
		  `send_date` datetime NOT NULL,
		  `update_date` datetime NULL,
		  `text` text NOT NULL,
		  `is_read` tinyint(1) NOT NULL DEFAULT '0'
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `messaging` ADD PRIMARY KEY (`id`), ADD KEY `organization_id` (`organization_id`);
		ALTER TABLE `messaging` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

    	");
    }
}
