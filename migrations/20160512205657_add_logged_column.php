<?php

use \Console\Migration\BaseMigration;

class AddLoggedColumn extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `user_session`
              ADD COLUMN `logged_via` INT NULL DEFAULT NULL AFTER `active`;
        ");

        $this->query("
            ALTER TABLE `user`
                DROP COLUMN `current_auth_session`,
                DROP COLUMN `current_api_auth_session`;
        ");
    }
}
