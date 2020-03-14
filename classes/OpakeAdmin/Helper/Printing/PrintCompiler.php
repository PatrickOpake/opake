<?php

namespace OpakeAdmin\Helper\Printing;

class PrintCompiler
{
	const MIME_TYPE_PDF = 'application/pdf';

	/**
	 * @var bool
	 */
	protected $cleanTemporaryFiles = true;

	/**
	 * @param bool $cleanTemporaryFiles
	 */
	public function setCleanTemporaryFiles($cleanTemporaryFiles)
	{
		$this->cleanTemporaryFiles = $cleanTemporaryFiles;
	}

	/**
	 * @return bool
	 */
	public function isCleanTemporaryFiles()
	{
		return $this->cleanTemporaryFiles;
	}

	/**
	 * @param \OpakeAdmin\Helper\Printing\Document[] $documents
	 * @return \Opake\Model\Document\PrintResult
	 */
	public function compile($documents)
	{
		$provider = new \OpakeAdmin\Helper\Printing\Provider\Local();
		$provider->setCleanTemporaryFiles($this->isCleanTemporaryFiles());
		return $provider->compile($documents);
	}
}