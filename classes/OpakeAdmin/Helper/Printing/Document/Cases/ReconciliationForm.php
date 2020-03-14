<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases;

use OpakeAdmin\Helper\Printing\Document;

class ReconciliationForm extends Document\PDFCompileDocument
{
	protected $reconciliationForm;

	/**
	 * @param $reconciliationForm
	 */
	public function __construct($reconciliationForm)
	{
		$this->reconciliationForm = $reconciliationForm;
	}


	public function getFileName()
	{
		return 'reconciliation-form.pdf';
	}

	protected function generateView()
	{
		$app = \Opake\Application::get();

		$view = $app->view('cases/export/case_medications_and_allergies');
		$view->reconciliation = $this->reconciliationForm;
		$view->allergies = [];
		$view->medications = [];
		$view->visit_updates = [];
		
		if ($view->reconciliation->id()) {
			$view->allergies = $this->reconciliationForm->allergies->find_all()->as_array();
			$view->medications = $this->reconciliationForm->medications->find_all()->as_array();
			$view->visit_updates = $this->reconciliationForm->visit_updates->find_all()->as_array();
		}

		return $view;
	}

}