<?php

namespace OpakeAdmin\Helper\Printing\Document;

use Opake\Model\UploadedFile;
use OpakeAdmin\Helper\Printing\Document;

abstract class CompileDocument extends Document
{
	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	public function runCompile()
	{
		$this->content = $this->compileContent();
	}

	public function getType()
	{
		return Document::TYPE_COMPILE_DOCUMENT;
	}

	abstract public function getFileName();

	abstract protected function compileContent();
}