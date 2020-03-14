<?php

use \Console\Migration\BaseMigration;

class UpdateCodingInsuranceTable extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_coding_insurance`
                ADD COLUMN `insurance_company_name` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `carrier_code` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `co_pay` DECIMAL(10,2) NULL DEFAULT NULL,
                ADD COLUMN `co_insurance` DECIMAL(10,2) NULL DEFAULT NULL;
        ");
    }
}
