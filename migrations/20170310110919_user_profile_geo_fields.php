<?php

use \Console\Migration\BaseMigration;

class UserProfileGeoFields extends BaseMigration
{
    public function change()
    {
	    $this->query("ALTER TABLE `user` DROP COLUMN `npi_number`;");
	    $this->query("
	       ALTER TABLE `user`
				ADD `state_id` int(11) NULL DEFAULT NULL;
			ALTER TABLE `user`
				ADD `custom_state` varchar(255) NULL DEFAULT NULL;
			ALTER TABLE `user`
				ADD `city_id` int(11) NULL DEFAULT NULL;
			ALTER TABLE `user`
				ADD `custom_city` varchar(255) NULL DEFAULT NULL;
			ALTER TABLE `user`
				ADD `zip_code` varchar(20) NULL DEFAULT NULL;
	    ");
    }
}
