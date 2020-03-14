<?php

use \Console\Migration\BaseMigration;

class CaseCodingFixAmount extends BaseMigration
{
    public function change()
    {
		$this->query("
			UPDATE `case_coding_bill` SET `amount` = 0 WHERE `amount` IS NULL;
			UPDATE `case_coding_bill` SET `charge` = 0 WHERE `charge` IS NULL;
		");
    }
}
