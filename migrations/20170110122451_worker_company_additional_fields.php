<?php

use \Console\Migration\BaseMigration;

class WorkerCompanyAdditionalFields extends BaseMigration
{
    public function change()
    {
	    $this->query("
          ALTER TABLE `insurance_data_workers_comp`
                ADD COLUMN `employee_id` varchar(255) DEFAULT NULL,
                ADD COLUMN `employer_name` varchar(255) DEFAULT NULL,
                ADD COLUMN `employer_address` varchar(255) DEFAULT NULL,
                ADD COLUMN `employer_city_id` int(11) DEFAULT NULL,
                ADD COLUMN `employer_state_id` int(11) DEFAULT NULL,
                ADD COLUMN `employer_zip` varchar(20) DEFAULT NULL;
        ");
    }
}
