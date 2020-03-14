<?php

use \Console\Migration\BaseMigration;

class ServiceCodes extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `organization`
                ADD COLUMN `eligible_service_codes` VARCHAR(255) NULL DEFAULT NULL AFTER `federal_tax`;
        ");
    }
}
