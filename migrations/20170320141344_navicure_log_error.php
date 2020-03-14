<?php

use \Console\Migration\BaseMigration;

class NavicureLogError extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_navicure_log` CHANGE `error` `error` VARCHAR(512) NULL DEFAULT NULL;
		");
    }
}
