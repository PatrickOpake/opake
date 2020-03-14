<?php

use \Console\Migration\BaseMigration;

class AddIndexesToCaseRegistration extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_registration`
                ADD INDEX `IDX_organization_id` (`organization_id`),
                ADD INDEX `IDX_case_id` (`case_id`),
                ADD INDEX `IDX_patient_id` (`patient_id`);
        ");
    }
}
