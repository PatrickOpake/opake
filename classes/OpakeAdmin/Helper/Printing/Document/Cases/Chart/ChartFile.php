<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases\Chart;

use Opake\Application;
use Opake\Model\UploadedFile;
use OpakeAdmin\Helper\Printing\Document\CompileDocument;
use OpakeAdmin\Helper\Printing\Document\FileDocument;
use OpakeAdmin\Helper\Printing\Utils\Chart\ChartDynamicFieldsWriter;
use OpakeAdmin\Helper\Printing\Utils\Chart\ChartTemporaryFile;

class ChartFile extends CompileDocument
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

	/**
	 * @return UploadedFile
	 */
	public function getFile()
	{
		return $this->form->file;
	}

	public function getFileName()
	{
		return $this->form->file->original_filename;
	}

	protected function compileContent()
	{
		$writer = new ChartDynamicFieldsWriter($this->form);
		if ($writer->hasDynamicFields()) {
			$tempFile = new ChartTemporaryFile($this->form);
			$tempFile->createFile();
			$writer->setCase($this->getCase());
			$writer->setInputFilePath($tempFile->getFilePath());
			$writer->writeFields();
			$content = $tempFile->readContent();
			$tempFile->cleanup();
			return $content;
		}

		return $this->form->file->readContent();
	}

	public function getContentMimeType()
	{
		return $this->form->file->mime_type;
	}
}