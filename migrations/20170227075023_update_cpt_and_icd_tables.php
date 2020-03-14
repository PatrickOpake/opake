<?php

use \Console\Migration\BaseMigration;

class UpdateCptAndIcdTables extends BaseMigration
{
    public function change()
    {
        $this->query("
	        ALTER TABLE `cpt` ADD COLUMN `concept_id` VARCHAR(50) DEFAULT NULL AFTER `name`;
	        
            CREATE TABLE IF NOT EXISTS `cpt_year` (
                `id` int(11) NOT NULL,
                `year` int(11) NULL DEFAULT NULL,
                `note` varchar(255) NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `cpt_year` ADD PRIMARY KEY (`id`);
            ALTER TABLE `cpt_year` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            
            CREATE TABLE IF NOT EXISTS `cpt_to_cpt_year` (
                `cpt_id` int(11) NOT NULL,
                `year_id` int(11) NOT NULL,
                `active` tinyint(2) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `cpt_to_cpt_year` ADD UNIQUE KEY `uni` (`cpt_id`,`year_id`);
            	        
            CREATE TABLE IF NOT EXISTS `icd_year` (
                `id` int(11) NOT NULL,
                `year` int(11) NULL DEFAULT NULL,
                `note` varchar(255) NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `icd_year` ADD PRIMARY KEY (`id`);
            ALTER TABLE `icd_year` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            
            CREATE TABLE IF NOT EXISTS `icd_to_icd_year` (
                `icd_id` int(11) NOT NULL,
                `year_id` int(11) NOT NULL,
                `active` tinyint(2) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `icd_to_icd_year` ADD UNIQUE KEY `uni` (`icd_id`,`year_id`);
            
            INSERT INTO cpt_year (year) VALUES (2016);
            INSERT INTO icd_year (year) VALUES (2016);
            INSERT INTO icd_year (year) VALUES (2017);
	    ");

        $cptRows = $this->getDb()->query('select')
            ->table('cpt')
            ->execute();

        $this->getDb()->begin_transaction();

        try {
            foreach ($cptRows as $row) {
                $this->getDb()->query('insert')
                    ->table('cpt_to_cpt_year')
                    ->data([
                        'cpt_id' => $row->id,
                        'year_id' => 1,
                        'active' => $row->active
                    ])->execute();
            }

            $this->getDb()->commit();
        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }

        $icdRows = $this->getDb()->query('select')
            ->table('icd')
            ->execute();

        $this->getDb()->begin_transaction();

        try {
            foreach ($icdRows as $row) {
                if ($row->is_2016) {
                    $this->getDb()->query('insert')
                        ->table('icd_to_icd_year')
                        ->data([
                            'icd_id' => $row->id,
                            'year_id' => 1,
                            'active' => 1
                        ])->execute();
                }
                if ($row->is_2017) {
                    $this->getDb()->query('insert')
                        ->table('icd_to_icd_year')
                        ->data([
                            'icd_id' => $row->id,
                            'year_id' => 2,
                            'active' => 1
                        ])->execute();
                }
            }

            $this->getDb()->commit();
        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }

    }
}
