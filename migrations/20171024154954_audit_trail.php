<?php

use \Console\Migration\BaseMigration;

class AuditTrail extends BaseMigration
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
	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_PAPER_CLAIMS_PRINT,
			    'name' => 'Print Paper Claims',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_CLICK_CHECK_ELIGIBILITY,
			    'name' => 'Click Check Eligibility',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CODING_PAGE_SAVED,
			    'name' => 'Save Coding',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CODING_PAGE_CLAIM_PRINT,
			    'name' => 'Print Claim',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CODING_PAGE_CLAIM_PREVIEW,
			    'name' => 'Preview Claim',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_NOTES_SAVED,
			    'name' => 'Note Saved',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_NOTES_EDITED,
			    'name' => 'Note Edited',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_NOTES_DELETED,
			    'name' => 'Note Delete',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_NOTES_SAVED,
			    'name' => 'Note Saved',
			    'zone' => 4
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_NOTES_EDITED,
			    'name' => 'Note Edited',
			    'zone' => 4
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_NOTES_DELETED,
			    'name' => 'Note Delete',
			    'zone' => 4
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_PATIENT_STATEMENT_GENERATED,
			    'name' => 'Generate Patient Statement',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_LEDGER_PAYMENTS_APPLIED,
			    'name' => 'Payments Applied',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_LEDGER_PAYMENTS_EDITED,
			    'name' => 'Payments Edited',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_VERIFICATION_EDIT,
			    'name' => 'Verification & Pre-Authorization Edit',
			    'zone' => 4
		    ])->execute();
    }
}
