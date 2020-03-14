<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases;

use OpakeAdmin\Helper\Printing\Document;

class FacesheetDocument extends Document\PDFCompileDocument
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
		return 'facesheet-' . $this->case->id() .'.pdf';
	}

	protected function generateView()
	{
		$app = \Opake\Application::get();
		$printPatientDetails = $printInsurances = true;

		$validationData = json_decode(json_encode($this->case->registration->toArray()));
		$patientsService = $app->services->get('Patients');
		$validationErrors = $patientsService->validate(
			'Cases_Registration',
			'Cases_Registration_Insurance',
			$validationData
		);

		$view = $app->view('cases/export/case');
		$view->case = $this->case;
		$view->registration = $this->case->registration;
		$view->organization = $this->case->organization;
		$view->printPatientDetails = $printPatientDetails;
		$view->printInsurances = $printInsurances;
		$view->validationErrors = $validationErrors;

		return $view;
	}


}