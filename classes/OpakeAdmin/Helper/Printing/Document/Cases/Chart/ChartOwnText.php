<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases\Chart;

use Opake\Application;
use OpakeAdmin\Helper\Chart\DynamicFieldsHelper;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart;
use OpakeAdmin\Helper\Printing\Document\PDFCompileDocument;
use OpakeAdmin\Helper\Printing\Utils\Chart\HeadersWriter;

class ChartOwnText extends PDFCompileDocument
{

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var \Opake\Model\Forms\Document
	 */
	protected $form;

	/**
	 * @param \Opake\Model\Cases\Item $form
	 * @param \Opake\Model\Forms\Document $case
	 */
	public function __construct($form, $case = null)
	{
		$this->form = $form;
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
	 * @return \Opake\Model\Forms\Document
	 */
	public function getForm()
	{
		return $this->form;
	}

	public function getFileName()
	{
		$filename = 'Chart_' . $this->form->id();
		if ($this->case) {
			$filename .= '_' . $this->case->id();
		}
		$filename .= '.pdf';

		return $filename;
	}


	protected function generateView()
	{
		$app = Application::get();
		$view = $app->view('settings/forms/charts/export/form');
		if ($this->case) {
			$view->case = $this->case;
		}

		$ownText = $this->form->own_text;
		if ($ownText && $this->case) {
			$dynamicFieldsHelper = new DynamicFieldsHelper($this->case);
			$ownText = $dynamicFieldsHelper->replaceDynamicFields($ownText);
		}

		$view->doc = $this->form;
		$view->ownText = $ownText;

		if ($this->case) {
			$view->org = $this->case->organization;
		} else {
			$view->org = $this->form->organization;
		}

		$view->landscape = $this->form->is_landscape;
		$view->pixelWidth = ($this->form->is_landscape) ? Chart::getProportionPixelWidth(Chart::A4_LANDSCAPE) : Chart::getProportionPixelWidth(Chart::A4_PORTRAIT);

		return $view;
	}

	protected function getPDFCompileOptions()
	{
		return [
			'landscape' => $this->form->is_landscape
		];
	}

	protected function prepareAfterCompiling()
	{
		if ($this->form->include_header) {
			$writer = new HeadersWriter($this->outputTemporaryFile);
			$writer->setOrganization($this->form->organization);
			if ($this->case) {
				$writer->setCase($this->case);
			}
			$writer->writeHeaders();
		}
	}
}