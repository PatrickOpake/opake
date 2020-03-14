<?php

use \Console\Migration\BaseMigration;

class NewRole extends BaseMigration
{
	public function change()
	{
		$this->query("
            INSERT INTO `role` (`id`, `name`) VALUES (2, 'Site Admin');
        ");
	}
}
