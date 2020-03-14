<?php

namespace OpakeAdmin\Helper\Printing\Document\Billing;

use OpakeAdmin\Helper\Printing\Document\CompileDocument;

class EOBDocument extends CompileDocument
{
	/**
	 * @var \Opake\Model\Billing\EOB
	 */
	protected $document;

	/**
	 * @param \Opake\Model\Billing\EOB $document
	 */
	public function __construct($document)
	{
		$this->document = $document;
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