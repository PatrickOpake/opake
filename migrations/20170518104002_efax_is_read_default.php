<?php

use \Console\Migration\BaseMigration;

class EfaxIsReadDefault extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `efax_inbound_fax` CHANGE `is_read` `is_read` TINYINT(4)  NOT NULL  DEFAULT '0';
		");
    }
}
