<?php

use \Console\Migration\BaseMigration;

class AlertsOnDashboard extends BaseMigration
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
	    $this->getDb()->query('update')->table('case_registration')
		    ->data([
			    'insurance_verified' => 0,
		    ])
		    ->where('insurance_verified', 'IS NULL', $this->getDb()->expr(''))
		    ->execute();

	    $this->getDb()->query('update')->table('case_registration')
		    ->data([
			    'is_pre_authorization_completed' => 0,
		    ])
		    ->where('is_pre_authorization_completed', 'IS NULL', $this->getDb()->expr(''))
		    ->execute();

	    $this->getDb()->query('update')->table('case_registration')
		    ->data([
			    'pre_certification_required' => 0,
		    ])
		    ->where('pre_certification_required', 'IS NULL', $this->getDb()->expr(''))
		    ->execute();

	    $this->getDb()->query('update')->table('case_registration')
		    ->data([
			    'pre_certification_obtained' => 0,
		    ])
		    ->where('pre_certification_obtained', 'IS NULL', $this->getDb()->expr(''))
		    ->execute();

	    $this->getDb()->query('update')->table('case_registration')
		    ->data([
			    'self_funded' => 0,
		    ])
		    ->where('self_funded', 'IS NULL', $this->getDb()->expr(''))
		    ->execute();

	    $this->getDb()->query('update')->table('case_registration')
		    ->data([
			    'is_oon_benefits_cap' => 0,
		    ])
		    ->where('is_oon_benefits_cap', 'IS NULL', $this->getDb()->expr(''))
		    ->execute();

	    $this->getDb()->query('update')->table('case_registration')
		    ->data([
			    'is_asc_benefits_cap' => 0,
		    ])
		    ->where('is_asc_benefits_cap', 'IS NULL', $this->getDb()->expr(''))
		    ->execute();

	    $this->getDb()->query('update')->table('case_registration')
		    ->data([
			    'is_clauses_pertaining' => 0,
		    ])
		    ->where('is_clauses_pertaining', 'IS NULL', $this->getDb()->expr(''))
		    ->execute();

	    $this->getDb()->query('update')->table('case_registration')
		    ->data([
			    'is_pre_existing_clauses' => 0,
		    ])
		    ->where('is_pre_existing_clauses', 'IS NULL', $this->getDb()->expr(''))
		    ->execute();
    }
}
