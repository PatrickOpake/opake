<?php

use \Console\Migration\BaseMigration;

class HcpcDatabase extends BaseMigration
{
    public function change()
    {
        $this->query("
		CREATE TABLE IF NOT EXISTS `hcpc` (
		`id` int(11) NOT NULL,
		  `code` varchar(10) DEFAULT NULL,
		  `seqnum` varchar(10) DEFAULT NULL,
		  `recid` varchar(10) DEFAULT NULL,
		  `long_description` text,
		  `short_description` text,
		  `price` float DEFAULT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `hcpc` ADD PRIMARY KEY (`id`);
		ALTER TABLE `hcpc` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
    	");
    }
}
