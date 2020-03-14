<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases\Chart;

use Opake\Application;
use OpakeAdmin\Helper\Printing\Document;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart;
use OpakeAdmin\Helper\Printing\PrintCompiler;
use OpakeAdmin\Helper\Printing\Document\CompileDocument;
use OpakeAdmin\Helper\Printing\Utils\Chart\ChartDynamicFieldsWriter;
use OpakeAdmin\Helper\Printing\Utils\Chart\ChartTemporaryFile;
use OpakeAdmin\Helper\Printing\Utils\Chart\HeadersWriter;

class ChartFileWithHeader extends CompileDocument
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
	 * @param \Opake\Model\Forms\Document $form
	 * @param \Opake\Model\Cases\Item $case
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

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->form->file->original_filename;
	}

	/**
	 * @return int
	 */
	public function getType()
	{
		return Document::TYPE_PDF_COMPILE_DOCUMENT;
	}

	/**
	 * @return string
	 */
	public function getContentMimeType()
	{
		return PrintCompiler::MIME_TYPE_PDF;
	}

	protected function compileContent()
	{
		$tempFile = new ChartTemporaryFile($this->form);
		$tempFile->createFile();

		$headersWriter = new HeadersWriter($tempFile->getFilePath());
		$headersWriter->setOrganization($this->form->organization);
		if ($this->case) {
			$headersWriter->setCase($this->case);
		}
		$headersWriter->writeHeaders();

		$writer = new ChartDynamicFieldsWriter($this->form);
		if ($writer->hasDynamicFields()) {
			$writer->setCase($this->case);
			$writer->setInputFilePath($tempFile->getFilePath());
			$writer->writeFields();
		}

		$result = $tempFile->readContent();
		$tempFile->cleanup();

		return $result;
	}

}