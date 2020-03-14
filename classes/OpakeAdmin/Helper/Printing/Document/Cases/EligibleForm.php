<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases;
use OpakeAdmin\Helper\Printing\Document\PDFCompileDocument;


class EligibleForm extends PDFCompileDocument
{
	/**
	 * @var mixed
	 */
	protected $eligibleData;

	/**
	 * @param mixed $eligibleData
	 */
	public function __construct($eligibleData)
	{
		$this->eligibleData = $eligibleData;
	}

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return 'EligibleForm.pdf';
	}

	/**
	 * @return \Opake\View\View
	 * @throws \Exception
	 */
	protected function generateView()
	{
		$app = \Opake\Application::get();

		$eligible = $this->eligibleData;

		$view = $app->view('cases/export/eligibility');
		$view->coverage = $eligible->getCoverageArray();
		$view->eligible = $eligible;
		return $view;
	}

}