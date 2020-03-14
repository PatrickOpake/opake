<?php

use \Console\Migration\BaseMigration;

class PaymentPostingNotes extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_payment_posting_applied_payment_note` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `payment_id` int(11) unsigned DEFAULT NULL,
			  `user_id` int(11) unsigned DEFAULT NULL,
			  `text` varchar(2048) DEFAULT NULL,
			  `time_added` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");
    }
}
