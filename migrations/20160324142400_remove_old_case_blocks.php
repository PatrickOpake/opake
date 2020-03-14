<?php

use \Console\Migration\BaseMigration;

class RemoveOldCaseBlocks extends BaseMigration
{
    public function change()
    {
        $this->query("
            DELETE FROM `case_blocking_item` WHERE blocking_id IN (SELECT id FROM `case_blocking` WHERE time_from is NULL);
            DELETE FROM `case_blocking` WHERE time_from is NULL;
        ");
    }
}
