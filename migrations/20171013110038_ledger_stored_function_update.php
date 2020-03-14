<?php

use \Console\Migration\BaseMigration;

class LedgerStoredFunctionUpdate extends BaseMigration
{
    public function change()
    {
	    $app = $this->getApp();
	    $this->execute(
		    file_get_contents($app->root_dir . '/sql/ledger_patient_outstanding_balance.sql')
	    );
    }
}
