<?php

namespace OpakeAdmin\Helper\Printing\Document\Common;

use OpakeAdmin\Helper\Printing\Document\CompileDocument;

class ContentPDFDocument extends CompileDocument
{
	public function __construct($content)
	{
		$this->content = $content;
	}

	public function getFileName()
	{
		return 'file.pdf';
	}

	public function getContentMimeType()
	{
		return 'application/pdf';
	}

	public function runCompile()
	{

	}

	protected function compileContent()
	{

	}
}