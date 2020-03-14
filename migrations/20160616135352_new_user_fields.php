<?php

use \Console\Migration\BaseMigration;

class NewUserFields extends BaseMigration
{

    public function change()
    {
        $this->query('

			ALTER TABLE `user` ADD `practice_name` VARCHAR(100) NULL AFTER `comment`;
			ALTER TABLE `user` ADD `dea_number` VARCHAR(100) NULL AFTER `practice_name`;
			ALTER TABLE `user` ADD `dea_number_exp_date` DATE NULL AFTER `dea_number`;
			ALTER TABLE `user` ADD `medical_licence_number` VARCHAR(100) NULL AFTER `dea_number_exp_date`;
			ALTER TABLE `user` ADD `medical_licence_number_exp_date` DATE NULL AFTER `medical_licence_number`;
			ALTER TABLE `user` ADD `cds_number` VARCHAR(100) NULL AFTER `medical_licence_number_exp_date`;
			ALTER TABLE `user` ADD `cds_number_exp_date` DATE NULL AFTER `cds_number`;

');

    }
}
