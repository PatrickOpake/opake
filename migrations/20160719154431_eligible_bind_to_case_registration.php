<?php

use \Console\Migration\BaseMigration;

class EligibleBindToCaseRegistration extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `eligible_coverage_cache`
                RENAME TO `case_eligible_coverage`,
                ADD COLUMN `case_registration_id` INT(10) NOT NULL DEFAULT '0' AFTER `id`,
                DROP COLUMN `organization_id`,
                DROP COLUMN `hash`;
        ");

        $this->query("
            ALTER TABLE `case_eligible_coverage`
                ADD COLUMN `case_insurance_id` INT(10) NOT NULL DEFAULT '0' AFTER `case_registration_id`;
        ");

        $this->query("
           ALTER TABLE `case_eligible_coverage`
            ADD INDEX `IDX_case_registration_id` (`case_registration_id`);
        ");
    }
}
