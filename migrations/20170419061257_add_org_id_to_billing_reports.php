<?php

use \Console\Migration\BaseMigration;

class AddOrgIdToBillingReports extends BaseMigration
{
    public function change()
    {
        $this->query("
	        ALTER TABLE `billing_cases_report` ADD COLUMN `organization_id` INT(11) NULL DEFAULT NULL AFTER `case_id`;
	        ALTER TABLE `billing_procedures_report` ADD COLUMN `organization_id` INT(11) NULL DEFAULT NULL AFTER `case_id`;
        ");

        $app = $this->getApp();

        $caseReports = $app->orm->get('Billing_Report_Cases')
            ->find_all();
        $procedureReports = $app->orm->get('Billing_Report_Procedures')
            ->find_all();
        $this->getDb()->begin_transaction();

        try {
            foreach ($caseReports as $caseReport) {
                if ($caseReport->case_id) {
                    $caseReport->organization_id = $caseReport->case->organization_id;
                    $caseReport->save();
                }
            }
            foreach ($procedureReports as $procedureReport) {
                if ($procedureReport->case_id) {
                    $procedureReport->organization_id = $procedureReport->case->organization_id;
                    $procedureReport->save();
                }
            }
            $this->getDb()->commit();

        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }

    }
}
