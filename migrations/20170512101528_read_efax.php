<?php

use \Console\Migration\BaseMigration;

class ReadEfax extends BaseMigration
{
    public function change()
    {
		$this->query("DROP TABLE `efax_inbound_fax_read_status`;");
	    $this->query("ALTER TABLE `efax_inbound_fax` ADD `is_read` TINYINT  NULL  DEFAULT NULL  AFTER `saved_file_id`;");
    }
}
