<?php

namespace OpakeAdmin\Helper\Printing\Document;

use Opake\Model\UploadedFile;
use OpakeAdmin\Helper\Printing\Document;

abstract class FileDocument extends Document
{

	public function getType()
	{
		return Document::TYPE_FILE_DOCUMENT;
	}

	public function getContentMimeType()
	{
		return $this->getFile()->mime_type;
	}

	/**
	 * @return UploadedFile
	 * @throws \Exception
	 */
	abstract public function getFile();
}