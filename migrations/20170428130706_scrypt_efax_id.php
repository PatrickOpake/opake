<?php

use \Console\Migration\BaseMigration;

class ScryptEfaxId extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `efax_inbound_fax`
				CHANGE `scrypt_sfax_id` `scrypt_sfax_id` VARCHAR(50)  NULL  DEFAULT NULL;
		");
    }
}
