<?php

use \Console\Migration\BaseMigration;

class BookingPatientInsuranceTable extends BaseMigration
{
    public function change()
    {
	    $this->query("
            CREATE TABLE `booking_patient_insurance_types` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `booking_patient_id` INT(10) NULL,
                `type` INT(10) NULL,
                `order` INT(10) NULL,
                 `selected_insurance_id` INT(10) NULL,
                `insurance_data_id` INT(10) NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
