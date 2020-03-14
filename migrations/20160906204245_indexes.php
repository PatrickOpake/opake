<?php

use \Console\Migration\BaseMigration;

class Indexes extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_registration_insurance_types`
                ADD INDEX `IDX_registration_id` (`registration_id`);

            ALTER TABLE `booking_insurance_types`
                ADD INDEX `IDX_booking_id` (`booking_id`);

            ALTER TABLE `case_registration_insurance_types`
                ADD INDEX `IDX_registration_id` (`registration_id`);

        ");

        $this->query("
            ALTER TABLE `user_session`
                ADD INDEX `IDX_user_id` (`user_id`);

            ALTER TABLE `user_session`
                ADD INDEX `IDX_user_id_active_logged_via` (`user_id`, `active`, `logged_via`)
        ");

        $this->query("
            ALTER TABLE `case_drivers_license`
                ADD INDEX `IDX_case_id` (`case_id`);
        ");

        $this->query("
            ALTER TABLE `case_insurance_card`
                ADD INDEX `IDX_case_id` (`case_id`);
        ");


    }
}
