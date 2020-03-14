<?php

namespace OpakeAdmin\Helper\Printing\Provider;

abstract class AbstractProvider
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var bool
	 */
	protected $cleanTemporaryFiles = true;

	public function __construct()
	{
		$this->pixie = \Opake\Application::get();
	}

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
	 */
	abstract public function compile($documents);
}

