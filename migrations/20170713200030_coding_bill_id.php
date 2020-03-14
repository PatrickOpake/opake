<?php

use \Console\Migration\BaseMigration;

class CodingBillId extends BaseMigration
{

    public function change()
    {
        $this->query("
            ALTER TABLE `billing_ledger_payment_activity`
                CHANGE COLUMN `applied_payment_id` `coding_bill_id` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `id`;
        ");
    }
}
