<?php

use \Console\Migration\BaseMigration;

class RemoveSiteAdmin extends BaseMigration
{
	public function change()
	{
		$this->query("DELETE FROM role WHERE id=2");
		$this->query("UPDATE `user` SET role_id=1 WHERE role_id=2");
	}
}
