<?php

use \Console\Migration\BaseMigration;

class AddYiddishAndHebrewLanguages extends BaseMigration
{
	public function change()
	{
		$this->query("
            INSERT INTO language (name) VALUES ('Yiddish');
            INSERT INTO language (name) VALUES ('Hebrew');
        ");
	}
}
