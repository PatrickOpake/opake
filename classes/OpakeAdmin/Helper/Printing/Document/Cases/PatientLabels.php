<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases;

use OpakeAdmin\Helper\Printing\Document;

class PatientLabels extends Document\PDFCompileDocument
{
	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;


	/**
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function __construct($case)
	{
		$this->case = $case;
	}

	public function getFileName()
	{
		return 'patient-labels-' . $this->case->id() .'.pdf';
	}

	protected function generateView()
	{
		$app = \Opake\Application::get();
		$view = $app->view('cases/export/patient_labels');
		$view->case = $this->case;
		$view->patient = $this->case->registration->patient;

		return $view;
	}


	protected function getPDFCompileOptions()
	{
		return [
			'page_size' => 'Letter',
			'margins' => '-L 0.1875in -R 0.1875in -T 0.4in -B 0.25in'
		];
	}
}