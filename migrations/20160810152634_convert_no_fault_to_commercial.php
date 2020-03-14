<?php

use \Console\Migration\BaseMigration;

class ConvertNoFaultToCommercial extends BaseMigration
{
    public function change()
    {
        $this->getDb()->query('update')
            ->table('booking_insurance_types')
            ->data([
                'type' => 1
            ])
            ->where('type', 4)
            ->execute();

        $this->getDb()->query('update')
            ->table('case_registration_insurance_types')
            ->data([
                'type' => 1
            ])
            ->where('type', 4)
            ->execute();

        $this->getDb()->query('update')
            ->table('patient_insurance_types')
            ->data([
                'type' => 1
            ])
            ->where('type', 4)
            ->execute();
    }
}
