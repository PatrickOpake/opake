<?php

namespace OpakeAdmin\Controller\Document;

use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;

class PrintResult extends \OpakeAdmin\Controller\AuthPage
{
	public function actionPrintResult()
	{

		$resultId = $this->request->param('subid');
		$download = (bool) $this->request->get('download');
		$key = $this->request->get('key');

		$printResult = $this->orm->get('Document_PrintResult', $resultId);
		if (!$printResult->loaded()) {
			throw new PageNotFound('Print result is not found');
		}

		$file = $printResult->file;

		if (!$file->loaded()) {
			throw new PageNotFound('File is not found');
		}

		if ($key !== $printResult->key) {
			throw new BadRequest('Incorrect access key');
		}

		$fileName = $file->original_filename;

		$this->view = null;
		$downloadDocument = $download;
		$isInline = !$download;
		$this->response->file($file->mime_type, $fileName, $file->readContent(), $downloadDocument, $isInline);
	}
}