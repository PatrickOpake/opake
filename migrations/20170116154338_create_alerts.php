<?php

use \Console\Migration\BaseMigration;

class CreateAlerts extends BaseMigration
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
	    CREATE TABLE IF NOT EXISTS `site_alert` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `site_id` int(11) NOT NULL,
                `enable_for_site` tinyint(1) DEFAULT '0',
                `cases_report_completed_48hrs_case_end` tinyint(1) DEFAULT '0',
                `schedule_patient_not_insurance_verified` tinyint(1) DEFAULT '0',
                `schedule_patient_not_completed_preauthorized` tinyint(1) DEFAULT '0',
                `schedule_patient_has_pre_certification_required` tinyint(1) DEFAULT '0',
                `schedule_patient_has_not_been_pre_certified` tinyint(1) DEFAULT '0',
                `schedule_patient_is_self_funded` tinyint(1) DEFAULT '0',
                `schedule_patient_has_oon_benefits` tinyint(1) DEFAULT '0',
                `schedule_patient_has_asc_benefits` tinyint(1) DEFAULT '0',
                `schedule_patient_has_clauses_under_medicare_entitlement` tinyint(1) DEFAULT '0',
                `registration_not_completed` tinyint(1) DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
            
	    CREATE TABLE IF NOT EXISTS `cases_alert` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `case_id` int(11) NOT NULL,
                `type` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
            
            CREATE TABLE IF NOT EXISTS `registration_alert` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `registration_id` int(11) NOT NULL,
	       	`type` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
	");
    }
}
