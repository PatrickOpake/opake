<?php

use \Console\Migration\BaseMigration;

class AduitClaimActivity extends BaseMigration
{
    public function change()
    {
	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_CLAIM_PAPER_UB04_SENT,
			    'name' => 'Submit paper UB04',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_CLAIM_PAPER_1500_SENT,
			    'name' => 'Submit paper 1500',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_CLAIM_ELECTRONIC_UB04_SENT,
			    'name' => 'Submit electronic UB04',
			    'zone' => 5
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_CLAIM_ELECTRONIC_1500_SENT,
			    'name' => 'Submit electronic 1500',
			    'zone' => 5
		    ])->execute();
    }
}
