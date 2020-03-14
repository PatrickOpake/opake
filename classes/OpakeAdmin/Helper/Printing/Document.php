<?php

namespace OpakeAdmin\Helper\Printing;

use Opake\Model\UploadedFile;

abstract class Document
{
	const TYPE_FILE_DOCUMENT = 1;
	const TYPE_COMPILE_DOCUMENT = 2;
	const TYPE_PDF_COMPILE_DOCUMENT = 3;

	abstract public function getType();

	abstract public function getContentMimeType();
}