<?php

use \Console\Migration\BaseMigration;

class PreProdCleanup extends BaseMigration
{
    public function change()
    {
        $this->query("
            DROP TABLE `case_stage`;
            DROP TABLE `case_hp`;
        ");
    }
}
