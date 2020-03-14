<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases\Chart;

use Opake\Application;
use OpakeAdmin\Helper\Chart\DynamicFieldsHelper;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart;
use OpakeAdmin\Helper\Printing\Document\PDFCompileDocument;
use OpakeAdmin\Helper\Printing\Utils\Chart\HeadersWriter;

class MultipleChartOwnText extends PDFCompileDocument
{
	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var \Opake\Model\Forms\Document[]
	 */
	protected $forms;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 * @param \Opake\Model\Forms\Document[] $forms
	 */
	public function __construct($forms, $case = null)
	{
		$this->forms = $forms;
		$this->case = $case;
	}

	/**
	 * @return \Opake\Model\Cases\Item
	 */
	public function getCase()
	{
		return $this->case;
	}

	/**
	 * @return \Opake\Model\Forms\Document[]
	 */
	public function getForms()
	{
		return $this->forms;
	}

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return 'print-result.pdf';
	}

	/**
	 * @return \Opake\View\View
	 */
	protected function generateView()
	{
		$app = Application::get();
		$view = $app->view('settings/forms/charts/export/multiple-form');

		$dataSets = [];
		foreach ($this->forms as $form) {

			$dataSet = [];

			if ($this->case) {
				$dataSet['case'] = $this->case;
			}

			$ownText = $form->own_text;
			if ($ownText && $this->case) {
				$dynamicFieldsHelper = new DynamicFieldsHelper($this->case);
				$ownText = $dynamicFieldsHelper->replaceDynamicFields($ownText);
			}

			$dataSet['doc'] = $form;
			$dataSet['ownText'] = $ownText;
			$dataSet['pixelWidth'] = ($form->is_landscape) ? Chart::getProportionPixelWidth(Chart::A4_LANDSCAPE) : Chart::getProportionPixelWidth(Chart::A4_PORTRAIT);

			if ($this->case) {
				$dataSet['org'] = $this->case->organization;
			} else {
				$dataSet['org'] = $form->organization;
			}

			$dataSets[] = $dataSet;
		}

		$view->dataSets = $dataSets;

		return $view;
	}

	protected function getPDFCompileOptions()
	{
		return [
			'landscape' => $this->forms[0]->is_landscape
		];
	}

	protected function prepareAfterCompiling()
	{
		if ($this->forms[0]->include_header) {
			$writer = new HeadersWriter($this->outputTemporaryFile);
			$writer->setOrganization($this->forms[0]->organization);
			if ($this->case) {
				$writer->setCase($this->case);
			}
			$writer->writeHeaders();
		}
	}
}