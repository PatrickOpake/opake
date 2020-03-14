<?php

use \Console\Migration\BaseMigration;

class AddBulkPrint extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `patient_statement_history`
				ADD `is_bulk_print` TINYINT(4)  NULL  DEFAULT '0'  AFTER `print_result_id`;
		");
    }
}
