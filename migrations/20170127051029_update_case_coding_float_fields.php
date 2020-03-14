<?php

use \Console\Migration\BaseMigration;

class UpdateCaseCodingFloatFields extends BaseMigration
{
    public function change()
    {
        $this->query('
			ALTER TABLE `case_coding_bill` CHANGE `charge` `charge` DECIMAL(10,2) NULL DEFAULT NULL,
			    CHANGE `amount` `amount` DECIMAL(10,2) NULL DEFAULT NULL;
			    
            ALTER TABLE `case_coding_value` CHANGE `amount` `amount` DECIMAL(10,2) NULL DEFAULT NULL;
        ');
    }
}
