<?php

use \Console\Migration\BaseMigration;

class AddNewProtectedType extends BaseMigration
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
	    $q = $this->getDb()->query('select')->table('case_chart')
		    ->fields('id', 'uploaded_file_id')
		    ->execute();

	    foreach ($q as $row) {

		    $this->getDb()->query('update')->table('uploaded_files')
			    ->data([
				    'protected_type' => 'cases_chart'
			    ])
			    ->where('id', $row->uploaded_file_id)
			    ->execute();
	    }

	    $q = $this->getDb()->query('select')->table('case_financial_document')
		    ->fields('id', 'uploaded_file_id')
		    ->execute();

	    foreach ($q as $row) {

		    $this->getDb()->query('update')->table('uploaded_files')
			    ->data([
				    'protected_type' => 'cases_financial_document'
			    ])
			    ->where('id', $row->uploaded_file_id)
			    ->execute();
	    }



	    $q = $this->getDb()->query('select')->table('patient_financial_document')
		    ->fields('id', 'uploaded_file_id')
		    ->execute();

	    foreach ($q as $row) {

		    $this->getDb()->query('update')->table('uploaded_files')
			    ->data([
				    'protected_type' => 'patient_financial_document'
			    ])
			    ->where('id', $row->uploaded_file_id)
			    ->execute();
	    }

	    $q = $this->getDb()->query('select')->table('patient_chart')
		    ->fields('id', 'uploaded_file_id')
		    ->execute();

	    foreach ($q as $row) {

		    $this->getDb()->query('update')->table('uploaded_files')
			    ->data([
				    'protected_type' => 'patient_chart'
			    ])
			    ->where('id', $row->uploaded_file_id)
			    ->execute();
	    }
    }
}
