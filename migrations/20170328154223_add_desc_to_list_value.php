<?php

use \Console\Migration\BaseMigration;

class AddDescToListValue extends BaseMigration
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
	    $app = $this->getApp();

	    $reportTemplates = $app->orm->get('Cases_OperativeReport_ReportTemplate')
		    ->find_all();
	    $this->getDb()->begin_transaction();

	    try {
		    foreach ($reportTemplates as $template) {
			    $list_value = json_decode($template->list_value, true);
			    if(!empty($list_value)) {
				if(isset($list_value['column1'])) {
					foreach ($list_value['column1'] as $key => $item) {
						$list_value['column1'][$key]['description'] = '';
					}
				}
				if(isset($list_value['column2'])) {
					foreach ($list_value['column2'] as $key => $item) {
						$list_value['column2'][$key]['description'] = '';
					}
				}

				$template->list_value = json_encode($list_value);
				$template->save();

			    }
		    }
		    $this->getDb()->commit();

	    } catch (\Exception $e) {
		    $this->getDb()->rollback();
		    throw $e;

	    }



	    $reportTemplates = $app->orm->get('Cases_OperativeReport_Future_Template')
		    ->find_all();
	    $this->getDb()->begin_transaction();

	    try {
		    foreach ($reportTemplates as $template) {
			    $list_value = json_decode($template->list_value, true);
			    if(!empty($list_value)) {
				    if(isset($list_value['column1'])) {
					    foreach ($list_value['column1'] as $key => $item) {
						    $list_value['column1'][$key]['description'] = '';
					    }
				    }
				    if(isset($list_value['column2'])) {
					    foreach ($list_value['column2'] as $key => $item) {
						    $list_value['column2'][$key]['description'] = '';
					    }
				    }

				    $template->list_value = json_encode($list_value);
				    $template->save();

			    }
		    }
		    $this->getDb()->commit();

	    } catch (\Exception $e) {
		    $this->getDb()->rollback();
		    throw $e;
	    }

    }
}
