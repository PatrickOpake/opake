<?php

use \Console\Migration\BaseMigration;

class FixPrefCardEmptyNames extends BaseMigration
{
    public function change()
    {
        $this->query("
		    UPDATE `pref_card_staff` SET name = '' WHERE `name` IS NULL;
	    ");
    }
}
