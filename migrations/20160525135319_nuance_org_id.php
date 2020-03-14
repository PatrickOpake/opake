<?php

use \Console\Migration\BaseMigration;

class NuanceOrgId extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `organization`
                ADD COLUMN `nuance_org_id` VARCHAR(255) NULL DEFAULT NULL AFTER `logo_id`;
        ");
    }
}