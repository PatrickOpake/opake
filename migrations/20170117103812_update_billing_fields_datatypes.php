<?php

use \Console\Migration\BaseMigration;

class UpdateBillingFieldsDatatypes extends BaseMigration
{
    public function change()
    {
        $this->query('
			ALTER TABLE `case_coding` CHANGE `lab_services_outside_amount` `lab_services_outside_amount` DECIMAL(10,2) NULL DEFAULT NULL,
			    CHANGE `amount_paid` `amount_paid` DECIMAL(10,2) NULL DEFAULT NULL;
			    
            ALTER TABLE `case_coding_insurance` CHANGE `prior_payments` `prior_payments` DECIMAL(10,2) NULL DEFAULT NULL;
        ');
    }
}
