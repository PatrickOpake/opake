<?php

use \Console\Migration\BaseMigration;

class UpdateCaseSurgeonsFields extends BaseMigration
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
            ALTER TABLE `case` DROP `co_surgeon`, DROP `supervising_surgeon`, DROP `first_assistant_surgeon`, DROP `assistant`, DROP `anesthesiologist`, DROP `dictated_by`;

            CREATE TABLE IF NOT EXISTS `case_co_surgeon` (
                    `co_surgeon_id` int(11) NOT NULL,
                    `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_co_surgeon`
                    ADD UNIQUE KEY `uni` (`co_surgeon_id`,`case_id`);

            CREATE TABLE IF NOT EXISTS `case_supervising_surgeon` (
                    `supervising_surgeon_id` int(11) NOT NULL,
                    `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_supervising_surgeon`
                    ADD UNIQUE KEY `uni` (`supervising_surgeon_id`,`case_id`);

            CREATE TABLE IF NOT EXISTS `case_first_assistant_surgeon` (
                    `assistant_surgeon_id` int(11) NOT NULL,
                    `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_first_assistant_surgeon`
                    ADD UNIQUE KEY `uni` (`assistant_surgeon_id`,`case_id`);

            CREATE TABLE IF NOT EXISTS `case_assistant` (
                    `assistant_id` int(11) NOT NULL,
                    `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_assistant`
                    ADD UNIQUE KEY `uni` (`assistant_id`,`case_id`);

            CREATE TABLE IF NOT EXISTS `case_anesthesiologist` (
                    `anesthesiologist_id` int(11) NOT NULL,
                    `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_anesthesiologist`
                    ADD UNIQUE KEY `uni` (`anesthesiologist_id`,`case_id`);

            CREATE TABLE IF NOT EXISTS `case_dictated_by` (
                    `dictated_by_id` int(11) NOT NULL,
                    `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_dictated_by`
                    ADD UNIQUE KEY `uni` (`dictated_by_id`,`case_id`);
        ");
	}
}
