<?php

namespace OpakeAdmin\Helper\Printing\Document\Common;

use Opake\Model\UploadedFile;
use OpakeAdmin\Helper\Printing\Document\FileDocument;

class UploadedFileDocument extends FileDocument
{
	/**
	 * @var UploadedFile
	 */
	protected $file;

	/**
	 * @param UploadedFile $file
	 */
	public function __construct($file)
	{
		$this->file = $file;
	}

	/**
	 * @return UploadedFile
	 * @throws \Exception
	 */
	public function getFile()
	{
		return $this->file;
	}
}