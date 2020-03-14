<?php

use \Console\Migration\BaseMigration;

class AddSiteIdToChargeMaster extends BaseMigration
{
    public function change()
    {
        $this->query('
            ALTER TABLE `master_charge` 
                ADD COLUMN `site_id` INT(11) NULL DEFAULT NULL AFTER `organization_id`,
                CHANGE `organization_id` `organization_id` INT(11) NULL DEFAULT NULL;
        ');

    }
}
