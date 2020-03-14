<?php

use \Console\Migration\BaseMigration;

class UserDictationEnabledProperty extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `user` ADD `is_dictation_enabled` TINYINT NOT NULL DEFAULT '0';
        ");
    }
}
