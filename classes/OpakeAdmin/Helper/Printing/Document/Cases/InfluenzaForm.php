<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases;

use OpakeAdmin\Helper\Printing\Document;

class InfluenzaForm extends Document\PDFCompileDocument
{

	protected $influenzaForm;

	/**
	 * @param $influenzaForm
	 */
	public function __construct($influenzaForm)
	{
		$this->influenzaForm = $influenzaForm;
	}


	public function getFileName()
	{
		return 'influenza-form.pdf';
	}

	protected function generateView()
	{
		$app = \Opake\Application::get();
		$view = $app->view('cases/export/patient_influenza_form');
		$view->form = $this->influenzaForm;

		return $view;
	}

}