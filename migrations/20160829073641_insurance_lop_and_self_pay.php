<?php

use \Console\Migration\BaseMigration;

class InsuranceLopAndSelfPay extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `insurance_data_description` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `description` MEDIUMTEXT NULL,
                PRIMARY KEY (`id`)
            )
            COLLATE='utf8_general_ci'
            ENGINE=InnoDB;
        ");

        $this->getDb()->query('update')
            ->table('booking_insurance_types')
            ->data([
                'type' => 1
            ])
            ->where('type', 5)
            ->execute();

        $this->getDb()->query('update')
            ->table('case_registration_insurance_types')
            ->data([
                'type' => 1
            ])
            ->where('type', 5)
            ->execute();

        $this->getDb()->query('update')
            ->table('patient_insurance_types')
            ->data([
                'type' => 1
            ])
            ->where('type', 5)
            ->execute();
    }
}
