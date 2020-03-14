<?php

use \Console\Migration\BaseMigration;

class CityAndInsuranceCompanyOrganizationId extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `geo_city`
                ADD COLUMN `organization_id` INT(11) NULL DEFAULT NULL AFTER `state_id`,
                ADD INDEX `IDX_organization_id` (`organization_id`),
                ADD INDEX `IDX_state_id_organization_id` (`state_id`, `organization_id`);
        ");

        $this->query("
            ALTER TABLE `insurance_payor`
                ADD COLUMN `organization_id` INT(11) NULL DEFAULT NULL AFTER `actual`,
                ADD INDEX `IDX_actual` (`actual`),
                ADD INDEX `IDX_actual_organization_id` (`actual`, `organization_id`);
        ");
    }
}
