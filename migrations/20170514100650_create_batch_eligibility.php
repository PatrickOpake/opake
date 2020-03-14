<?php

use \Console\Migration\BaseMigration;

class CreateBatchEligibility extends BaseMigration
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
			CREATE TABLE IF NOT EXISTS `case_batch_eligibility` (
				`id` int(11) NOT NULL,
				`organization_id` int(11) NOT NULL,
				`date_received` DATETIME NULL DEFAULT NULL,
				`entries_sent` tinyint(1) NULL DEFAULT '0',
				`eligible` tinyint(1)  NULL DEFAULT '0',
				`not_eligible` tinyint(1)  NULL DEFAULT '0',
				`insufficient_data` tinyint(1) NULL DEFAULT '0',
				`coverage` MEDIUMTEXT NULL DEFAULT NULL
                	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                	ALTER TABLE `case_batch_eligibility` ADD PRIMARY KEY (`id`);
                	ALTER TABLE `case_batch_eligibility` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
                	
                	CREATE TABLE IF NOT EXISTS `case_batch_eligibility_cases` (
				`batch_id` int(11) NOT NULL,
				`case_insurance_id` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            		ALTER TABLE `case_batch_eligibility_cases`
                    		ADD UNIQUE KEY `uni` (`batch_id`,`case_insurance_id`);
		");
    }
}
