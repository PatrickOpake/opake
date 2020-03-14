<?php

use \Console\Migration\BaseMigration;

class MultipleDiagnosis extends BaseMigration
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

            ALTER TABLE `case_registration` DROP `diagnosis`;
            CREATE TABLE IF NOT EXISTS `case_registration_admitting_diagnosis` (
                `id` int(11) NOT NULL,
                `reg_id` int(11) NOT NULL,
                `diagnosis_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_registration_admitting_diagnosis`
            ADD PRIMARY KEY (`id`),
            ADD KEY `reg_id` (`reg_id`),
            ADD KEY `diagnosis_id` (`diagnosis_id`);
            ALTER TABLE `case_registration_admitting_diagnosis` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

            ALTER TABLE `case_coding` DROP `admit_diagnosis_id`;
            CREATE TABLE IF NOT EXISTS `case_coding_admitting_diagnosis` (
                `id` int(11) NOT NULL,
                `coding_id` int(11) NOT NULL,
                `diagnosis_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_coding_admitting_diagnosis`
            ADD PRIMARY KEY (`id`),
            ADD KEY `coding_id` (`coding_id`),
            ADD KEY `diagnosis_id` (`diagnosis_id`);
            ALTER TABLE `case_coding_admitting_diagnosis` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

            ALTER TABLE `case_op_report` DROP `pre_op_diagnosis_id`;
            CREATE TABLE IF NOT EXISTS `case_op_report_pre_op_diagnosis` (
                `id` int(11) NOT NULL,
                `report_id` int(11) NOT NULL,
                `diagnosis_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_op_report_pre_op_diagnosis`
            ADD PRIMARY KEY (`id`),
            ADD KEY `report_id` (`report_id`),
            ADD KEY `diagnosis_id` (`diagnosis_id`);
            ALTER TABLE `case_op_report_pre_op_diagnosis` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

            ALTER TABLE `case_op_report` DROP `post_op_diagnosis_id`;
            CREATE TABLE IF NOT EXISTS `case_op_report_post_op_diagnosis` (
                `id` int(11) NOT NULL,
                `report_id` int(11) NOT NULL,
                `diagnosis_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_op_report_post_op_diagnosis`
            ADD PRIMARY KEY (`id`),
            ADD KEY `report_id` (`report_id`),
            ADD KEY `diagnosis_id` (`diagnosis_id`);
            ALTER TABLE `case_op_report_post_op_diagnosis` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


        ");
	}
}
