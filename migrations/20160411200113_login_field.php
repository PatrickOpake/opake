<?php

use \Console\Migration\BaseMigration;

class LoginField extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `user`
                ADD COLUMN `username` VARCHAR(100) NULL DEFAULT NULL AFTER `id`;
        ");
    }
}
