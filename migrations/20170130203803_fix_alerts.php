<?php

use \Console\Migration\BaseMigration;

class FixAlerts extends BaseMigration
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
		RENAME TABLE `site_alert` TO `site_alert_settings`;
		ALTER TABLE `cases_alert` ADD COLUMN `code` VARCHAR(255) NULL DEFAULT NULL;
	");


    	$oldNewAlerts = [
	    'cases_report_completed_48hrs_case_end' => 'cases_report_completed_48hrs_case_end',
	    'registration_not_completed' => 'registration_not_completed',
	    'schedule_patient_not_insurance_verified' => 'not_insurance_verified',
	    'schedule_patient_not_completed_preauthorized' => 'not_completed_preauthorized',
	    'schedule_patient_has_pre_certification_required' => 'has_pre_certification_required',
	    'schedule_patient_has_not_been_pre_certified' => 'has_not_been_pre_certified',
	    'schedule_patient_is_self_funded' => 'is_self_funded',
	    'schedule_patient_has_oon_benefits' => 'has_oon_benefits',
	    'schedule_patient_has_asc_benefits' => 'has_asc_benefits',
	    'schedule_patient_has_clauses_under_medicare_entitlement' => 'has_clauses_under_medicare_entitlement',
	    'schedule_patient_has_clauses_under_patient_policy' => 'has_clauses_under_patient_policy'
    	];

	$q = $this->getDb()->query('select')->table('cases_alert')
	    ->fields('id', 'type')
	    ->execute();

	foreach ($q as $row) {
	    $this->getDb()->query('update')->table('cases_alert')
		    ->data([
			    'code' => $oldNewAlerts[$row->type],
			    'type' => \Opake\Model\Cases\Alert::$alertTypes[$row->type],
		    ])
		    ->where('id', $row->id)
		    ->execute();
	}

	$q = $this->getDb()->query('select')->table('registration_alert')
	    ->fields('id', 'type', 'registration_id')
	    ->execute();

	foreach ($q as $row) {
		$reg_q = $this->getDb()->query('select')->table('case_registration')
			->fields('id', 'case_id')
			->where('id', $row->registration_id)
			->execute()->as_array();
		if($reg_q) {
			$this->getDb()->query('insert')->table('cases_alert')
				->data([
					'case_id' => $reg_q[0]->case_id,
					'code' => $oldNewAlerts[$row->type],
					'type' => \Opake\Model\Cases\Alert::$alertTypes[$oldNewAlerts[$row->type]],
				])
				->execute();
		}
	}


	$this->query("
		ALTER TABLE `site_alert_settings` CHANGE COLUMN `schedule_patient_not_insurance_verified` `not_insurance_verified` tinyint(1) DEFAULT '0';
		ALTER TABLE `site_alert_settings` CHANGE COLUMN `schedule_patient_not_completed_preauthorized` `not_completed_preauthorized` tinyint(1) DEFAULT '0';
		ALTER TABLE `site_alert_settings` CHANGE COLUMN `schedule_patient_has_pre_certification_required` `has_pre_certification_required` tinyint(1) DEFAULT '0';
		ALTER TABLE `site_alert_settings` CHANGE COLUMN `schedule_patient_has_not_been_pre_certified` `has_not_been_pre_certified` tinyint(1) DEFAULT '0';
		ALTER TABLE `site_alert_settings` CHANGE COLUMN `schedule_patient_is_self_funded` `is_self_funded` tinyint(1) DEFAULT '0';
		ALTER TABLE `site_alert_settings` CHANGE COLUMN `schedule_patient_has_oon_benefits` `has_oon_benefits` tinyint(1) DEFAULT '0';
		ALTER TABLE `site_alert_settings` CHANGE COLUMN `schedule_patient_has_asc_benefits` `has_asc_benefits` tinyint(1) DEFAULT '0';
		ALTER TABLE `site_alert_settings` CHANGE COLUMN `schedule_patient_has_clauses_under_medicare_entitlement` `has_clauses_under_medicare_entitlement` tinyint(1) DEFAULT '0';
		
	");
    	$this->query("
           ALTER TABLE `cases_alert` ADD INDEX `IDX_case_id` (`case_id`);
        ");

    	$this->query("
    		DROP TABLE `registration_alert`;
    		RENAME TABLE `cases_alert` TO `case_alert`;

    	");
    }
}
