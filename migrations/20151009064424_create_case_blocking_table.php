<?php

use \Console\Migration\BaseMigration;

class CreateCaseBlockingTable extends BaseMigration
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
            CREATE TABLE IF NOT EXISTS `case_blocking` (
                `id` int(11) NOT NULL,
                `organization_id` int(11) NOT NULL,
                `location_id` int(11) NOT NULL,
                `doctor_id` int(11) NOT NULL,
                `color` varchar(255) DEFAULT NULL,
                `duration` tinyint(4) DEFAULT NULL,
                `range_from` timestamp NULL DEFAULT NULL,
                `range_to` timestamp NULL DEFAULT NULL,
                `recurrence_every` tinyint(4) DEFAULT NULL,
                `recurrence_week_days` TEXT DEFAULT NULL,
                `recurrence_monthly_day` tinyint(4) DEFAULT NULL,
                `recurrence_monthly_week_day` tinyint(4) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_blocking` ADD PRIMARY KEY (`id`);
            ALTER TABLE `case_blocking` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
	}
}
