<?php

use \Console\Migration\BaseMigration;

class OrderEmails extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE `order_outgoing_mail` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `order_outgoing_item_id` INT(10) NULL,
                `subject` VARCHAR(2048) NULL,
                `body` MEDIUMTEXT NULL,
                PRIMARY KEY (`id`),
                INDEX `IDX_order_outgoing_item_id` (`order_outgoing_item_id`)
            )
            ENGINE=InnoDB;
        ");

		$this->query("
            CREATE TABLE `order_outgoing_mail_receiver` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `order_outgoing_mail_id` INT(10) NULL,
                `email` VARCHAR(2048) NULL,
                `receiver_type` INT(10) NULL,
                PRIMARY KEY (`id`),
                INDEX `IDX_order_outgoing_mail_id` (`order_outgoing_mail_id`)
            )
            ENGINE=InnoDB;
        ");
	}
}
