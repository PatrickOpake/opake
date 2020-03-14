<?php

use \Console\Migration\BaseMigration;

class InventoryPackDate extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `inventory_pack`
                CHANGE COLUMN `exp_date` `exp_date` DATE NULL DEFAULT NULL AFTER `order_item_id`;
            ");
	}
}
