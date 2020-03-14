<?php

use \Console\Migration\BaseMigration;

class AddSiteIdToInventoryAndMultiplier extends BaseMigration
{
    public function change()
    {
        $this->query('
            ALTER TABLE `inventory` 
                ADD COLUMN `site_id` INT(11) NULL DEFAULT NULL AFTER `organization_id`,
                CHANGE `organization_id` `organization_id` INT(11) NULL DEFAULT NULL;
            ALTER TABLE `inventory_multiplier` 
                ADD COLUMN `site_id` INT(11) NULL DEFAULT NULL AFTER `organization_id`,
                CHANGE `organization_id` `organization_id` INT(11) NULL DEFAULT NULL;
                
            ALTER TABLE `site` ADD `chargeable` FLOAT NULL DEFAULT NULL;
        ');
    }
}
