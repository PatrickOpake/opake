<?php

use \Console\Migration\BaseMigration;

class InventoryNewItemStatuses extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `inventory` ADD COLUMN `complete_status` TINYINT(4) NULL DEFAULT '0';
			ALTER TABLE `inventory` ADD COLUMN `origin` TINYINT(4) NULL DEFAULT '0';
		");

	    $db = $this->getDb();

	    $db->query('update')
		    ->table('inventory')
		    ->data([
			    'origin' => \Opake\Model\Inventory::ORIGIN_UNKNOWN,
			    'complete_status' => \Opake\Model\Inventory::COMPLETE_STATUS_COMPLETE
		    ])
	        ->execute();

	    $db->query('update')
		    ->table('inventory')
		    ->data([
			    'origin' => \Opake\Model\Inventory::ORIGIN_CUSTOM_RECORD,
		        'complete_status' => \Opake\Model\Inventory::COMPLETE_STATUS_INCOMPLETE
		    ])
		    ->where([
		        [
		            ['name', 'IS NULL', $db->expr('')],
			        ['or', ['name', '']],
		        ],
			    ['or', [
				    ['item_number', 'IS NULL', $db->expr('')],
				    ['or', ['item_number', '']],
			    ]],
				['or', ['type', 'IS NULL', $db->expr('')]]
		    ])
	        ->execute();

    }
}
