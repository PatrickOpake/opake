<?php

use \Console\Migration\BaseMigration;

class CurrentApiAuthSession extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `user`
	          ADD COLUMN `current_api_auth_session` VARCHAR(100) NULL DEFAULT NULL AFTER `current_auth_session`;
        ");
    }
}
