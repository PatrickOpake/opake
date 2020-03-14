<?php

use \Console\Migration\BaseMigration;

class FieldIdCoding extends BaseMigration
{
    public function change()
    {
        try {
            $this->query("
                ALTER TABLE `case_coding_supply`
                    CHANGE COLUMN `hcpcs` `hcpcs_id` INT(11) NULL DEFAULT NULL AFTER `qty`;
            ");
        } catch (\Exception $e) {

        }

    }
}
