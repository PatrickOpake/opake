<?php

use \Console\Migration\BaseMigration;

class EligibleCoverageCache extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `eligible_coverage_cache` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `organization_id` INT(10) NULL DEFAULT NULL,
                `hash` VARCHAR(50) NULL DEFAULT NULL,
                `coverage` MEDIUMTEXT NULL DEFAULT NULL,
                `updated` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX `IDX_organization_id_hash` (`organization_id`, `hash`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            ALTER TABLE `patient`
            ADD COLUMN `insurance_verified` TINYINT(1) NULL DEFAULT '0' AFTER `status`,
            ADD COLUMN `is_pre_authorization_completed` TINYINT(1) NULL DEFAULT '0' AFTER `insurance_verified`;
        ");

        $this->query("
            ALTER TABLE `case_registration`
            ADD COLUMN `insurance_verified` TINYINT(1) NULL DEFAULT '0' AFTER `coverage_type`,
            ADD COLUMN `is_pre_authorization_completed` TINYINT(1) NULL DEFAULT '0' AFTER `insurance_verified`;
        ");
    }
}
