<?php

use \Console\Migration\BaseMigration;

class AddTypeToClaim extends BaseMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
	    $this->query("
			ALTER TABLE `billing_navicure_claim` ADD `type` TINYINT(4)  NULL  DEFAULT NULL  AFTER `case_id`;
		");

	    $db = $this->getDb();

	    $db->begin_transaction();
	    try {

		    $electronicalQuery = $db->query('select')
			    ->table('billing_navicure_claim')
			    ->execute();

		    foreach ($electronicalQuery as $row) {
			    $db->query('update')
				    ->table('billing_navicure_claim')
				    ->data([
					    'type' => \Opake\Model\Billing\Navicure\Claim::TYPE_ELECTRONIC_1500_CLAIM
				    ])
				    ->where('id', $row->id)
				    ->execute();
		    }

		    $db->commit();
	    } catch (\Exception $e) {
		    $db->rollback();
	    }

	    $db->begin_transaction();
	    try {

		    $paperQuery = $db->query('select')
			    ->table('billing_paper_claim')
			    ->execute();

		    foreach ($paperQuery as $row) {
			    $db->query('insert')
				    ->table('billing_navicure_claim')
				    ->data([
					    'case_id' => $row->case_id,
					    'last_transaction_date' => $row->billing_date,
					    //'insurance_payer_id' => $row->insurance_payer_id,
					    'type' => $row->type
				    ])
				    ->execute();
		    }

		    $db->commit();
	    } catch (\Exception $e) {
		    $db->rollback();
	    }

    $this->query("
		  DROP TABLE `billing_paper_claim`;
	");


    }
}
