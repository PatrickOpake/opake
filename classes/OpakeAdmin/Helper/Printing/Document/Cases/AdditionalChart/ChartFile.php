<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases\AdditionalChart;

use Opake\Model\UploadedFile;
use OpakeAdmin\Helper\Printing\Document\CompileDocument;

class ChartFile extends CompileDocument
{

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var \Opake\Model\Cases\Registration\Document
	 */
	protected $document;

	/**
	 * @param \Opake\Model\Cases\Registration\Document $document
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function __construct($document, $case = null)
	{
		$this->document = $document;
		$this->case = $case;
	}

	public function getFileName()
	{
		return $this->document->file->original_filename;
	}

	protected function compileContent()
	{
		return $this->document->file->readContent();
	}

	public function getContentMimeType()
	{
		return $this->document->file->mime_type;
	}
}