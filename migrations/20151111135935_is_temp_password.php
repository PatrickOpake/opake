<?php

use \Console\Migration\BaseMigration;

class IsTempPassword extends BaseMigration
{

	public function change()
	{
		$this->query("
            ALTER TABLE `user`
	          ADD COLUMN `is_temp_password` TINYINT NOT NULL DEFAULT '0' AFTER `case_color`;
        ");
	}
}
