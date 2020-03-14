<?php

use \Console\Migration\BaseMigration;

class NewOperativeReport extends BaseMigration
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
		CREATE TABLE IF NOT EXISTS `case_op_report_template` (
		  `id` int(11) NOT NULL,
		  `organization_id` int(11) NOT NULL,
		  `field` varchar(255) NOT NULL,
		  `active` TINYINT(1) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `case_op_report_template` ADD PRIMARY KEY (`id`), ADD KEY `organization_id` (`organization_id`);
		ALTER TABLE `case_op_report_template` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

                CREATE TABLE IF NOT EXISTS `case_op_report` (
		  `id` int(11) NOT NULL,
		  `case_id` int(11) NOT NULL,
		  `procedure_id` INT(11) NULL,
		  `pre_op_diagnosis_id` INT(11) NULL,
		  `operation_time` VARCHAR(255) NULL,
		  `post_op_diagnosis` TEXT NULL,
		  `specimens_removed` TEXT NULL,
		  `anesthesia_administered` VARCHAR(255) NULL,
		  `ebl` VARCHAR(255) NULL,
		  `blood_transfused` VARCHAR(255) NULL,
		  `fluids` VARCHAR(255) NULL,
		  `drains` VARCHAR(255) NULL,
		  `urine_output` VARCHAR(255) NULL,
		  `total_tourniquet_time` VARCHAR(255) NULL,
		  `consent` TEXT NULL,
		  `complications` TEXT NULL,
		  `clinical_history` TEXT NULL,
		  `approach` TEXT NULL,
		  `findings` TEXT NULL,
		  `description_procedure` TEXT NULL,
		  `follow_up_care` TEXT NULL,
		  `conditions_for_discharge` TEXT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `case_op_report` ADD PRIMARY KEY (`id`), ADD KEY `case_id` (`case_id`);
		ALTER TABLE `case_op_report` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

		CREATE TABLE IF NOT EXISTS `case_op_report_future_user` (
			`report_id` int(11) NOT NULL,
			`user_id` int(11) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		ALTER TABLE `case_op_report_future_user`
 			ADD UNIQUE KEY `uni` (`report_id`,`user_id`);

		 CREATE TABLE IF NOT EXISTS `case_op_report_future` (
		  `id` int(11) NOT NULL,
		  `organization_id` int(11) NOT NULL,
		  `cpt_id` INT(11) NULL,
		  `post_op_diagnosis` TEXT NULL,
		  `anesthesia_administered` VARCHAR(255) NULL,
		  `ebl` VARCHAR(255) NULL,
		  `drains` VARCHAR(255) NULL,
		  `consent` TEXT NULL,
		  `complications` TEXT NULL,
		  `approach` TEXT NULL,
		  `description_procedure` TEXT NULL,
		  `follow_up_care` TEXT NULL,
		  `conditions_for_discharge` TEXT NULL,
		  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `case_op_report_future` ADD PRIMARY KEY (`id`), ADD KEY `organization_id` (`organization_id`);
		ALTER TABLE `case_op_report_future` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
	}
}
