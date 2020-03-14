<?php

use \Console\Migration\BaseMigration;

class HpAsIntakeAdditional extends BaseMigration
{
	public function change()
	{
		$this->getDb()->query('insert')->table('case_registration_document_types')
			->data([
				'name' => 'H&P',
				'is_required' => 1
			])
			->execute();
	}
}
