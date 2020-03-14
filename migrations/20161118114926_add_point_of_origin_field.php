<?php

use \Console\Migration\BaseMigration;

class AddPointOfOriginField extends BaseMigration
{
    public function change()
    {
        $this->query("
			ALTER TABLE `booking_sheet` ADD `point_of_origin` INT(11) NULL DEFAULT NULL AFTER `description`;
			ALTER TABLE `case` ADD `point_of_origin` INT(11) NULL DEFAULT NULL;
		");
    }
}
