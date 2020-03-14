<?php

use \Console\Migration\BaseMigration;

class Remove835AutoPayments extends BaseMigration
{
    public function change()
    {
	    $db = $this->getDb();
	    $db->begin_transaction();
		try {

			$rows = $db->query('select')
				->table('billing_ledger_applied_payment')
				->where('claim_id', 'IS NOT NULL', $db->expr(''))
				->execute();

			foreach ($rows as $row) {
				$db->query('delete')
					->table('billing_ledger_payment_info')
					->where('id', $row->payment_info_id)
					->execute();

				$db->query('delete')
					->table('billing_ledger_applied_payment')
					->where('id', $row->id)
					->execute();
			}

			$db->commit();
		} catch (\Exception $e) {
			$db->rollback();
			throw $e;
		}
    }
}
