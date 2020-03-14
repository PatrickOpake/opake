<?php

use \Console\Migration\BaseMigration;

class RemoveCaseDocuments extends BaseMigration
{
	public function change()
	{
		$this->query('DELETE FROM case_hp;');
		$this->query('DELETE FROM case_discharge;');
	}
}
