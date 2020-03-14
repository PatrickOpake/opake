<?php

use \Console\Migration\BaseMigration;

class HcpcCodeUnique extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER IGNORE TABLE `hcpc` ADD UNIQUE(`code`);
        ");
    }
}
