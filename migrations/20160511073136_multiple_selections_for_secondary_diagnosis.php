<?php

use \Console\Migration\BaseMigration;

class MultipleSelectionsForSecondaryDiagnosis extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_registration` DROP `secondary_diagnosis_id`;
            CREATE TABLE IF NOT EXISTS `case_registration_secondary_diagnosis` (
                `id` int(11) NOT NULL,
                `reg_id` int(11) NOT NULL,
                `diagnosis_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_registration_secondary_diagnosis`
            ADD PRIMARY KEY (`id`),
            ADD KEY `reg_id` (`reg_id`),
            ADD KEY `diagnosis_id` (`diagnosis_id`);
            ALTER TABLE `case_registration_secondary_diagnosis` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            ");
    }
}
