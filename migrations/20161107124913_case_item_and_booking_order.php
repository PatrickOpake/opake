<?php

use \Console\Migration\BaseMigration;

class CaseItemAndBookingOrder extends BaseMigration
{
    public function change()
    {
	    $this->query("
            ALTER TABLE `case_other_staff` ADD `order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';
            ALTER TABLE `case_assistant` ADD `order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';
            ALTER TABLE `booking_other_staff` ADD `order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';
            ALTER TABLE `booking_assistant` ADD `order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';
            ALTER TABLE `booking_pre_op_required_data` ADD `order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';
            ALTER TABLE `booking_studies_ordered` ADD `order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';
            ALTER TABLE `case_pre_op_required_data` ADD `order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';
            ALTER TABLE `case_studies_ordered` ADD `order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';
        ");
    }
}
