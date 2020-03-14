<?php

use \Console\Migration\BaseMigration;

class CptIcdIndexes extends BaseMigration
{
    public function change()
    {
	    $this->query("
			ALTER TABLE `cpt_to_cpt_year` DROP INDEX `uni`;
			ALTER TABLE `cpt_to_cpt_year` ADD PRIMARY KEY (`cpt_id`, `year_id`);
	    ");

	    $this->query("
	       ALTER TABLE `icd_to_icd_year` DROP INDEX `uni`;
	       ALTER TABLE `icd_to_icd_year` ADD PRIMARY KEY (`icd_id`, `year_id`);
	    ");
    }
}
