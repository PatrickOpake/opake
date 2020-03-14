<?php

use \Console\Migration\BaseMigration;

class AddCurrentAuthSession extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `user`
            	ADD COLUMN `current_auth_session` VARCHAR(100) NULL DEFAULT NULL AFTER `hash`;
        ");
    }
}
