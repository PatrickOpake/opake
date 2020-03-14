<?php

use \Console\Migration\BaseMigration;

class InsuranceTypeCorrection extends BaseMigration
{
    public function change()
    {
		$this->getDb()->query('update')
			->table('insurance_payor')
			->data([
				'insurance_type' => null
			])
			->where('insurance_type', '0')
			->execute();
    }
}
