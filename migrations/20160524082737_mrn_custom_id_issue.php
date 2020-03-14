<?php

use \Console\Migration\BaseMigration;

class MrnCustomIdIssue extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `patient_mrn_counter`
                ADD COLUMN `id` INT(10) NOT NULL AUTO_INCREMENT FIRST,
                DROP PRIMARY KEY,
                ADD PRIMARY KEY (`id`),
                ADD UNIQUE INDEX `IDX_organization_id` (`organization_id`);
        ");
    }
}
