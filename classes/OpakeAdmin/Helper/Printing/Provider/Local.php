<?php

namespace OpakeAdmin\Helper\Printing\Provider;

use Opake\Application;
use Opake\Helper\TimeFormat;
use iio\libmergepdf\Merger;
use OpakeAdmin\Helper\Printing\Document\CompileDocument;
use OpakeAdmin\Helper\Printing\Document\FileDocument;
use OpakeAdmin\Helper\Printing\PrintCompiler;
use OpakeAdmin\Helper\Printing\PrintPool;

class Local extends AbstractProvider
{
	/**
	 * @param \OpakeAdmin\Helper\Printing\Document[] $documents
	 * @return \Opake\Model\Document\PrintResult
	 */
	public function compile($documents)
	{
		if (count($documents) == 1) {
			$document = reset($documents);
			return $this->compileSingleDocument($document);
		} else {
			return $this->compileMultipleDocuments($documents);
		}
	}

	/**
	 * @param \OpakeAdmin\Helper\Printing\Document $document
	 * @return \Opake\Model\Document\PrintResult
	 * @throws \Exception
	 */
	protected function compileSingleDocument($document)
	{
		if ($document instanceof FileDocument) {
			$uploadedFile = $document->getFile();
		} else if ($document instanceof CompileDocument) {
			$document->runCompile();
			$app = \Opake\Application::get();
			$uploadedFile = $app->orm->get('UploadedFile');
			$uploadedFile->storeContent($document->getFileName(), $document->getContent(), [
				'is_protected' => true,
				'is_assigned' => true,
				'mime_type' => $document->getContentMimeType()
			]);

			$uploadedFile->save();

			if ($this->isCleanTemporaryFiles()) {
				$this->addUploadedFileToCleaningQueue($uploadedFile);
			}

		} else {
			throw new \Exception('Unknown type of document');
		}

		/** @var \Opake\Model\Document\PrintResult $printResult */
		$printResult = $this->pixie->orm->get('Document_PrintResult');
		$printResult->uploaded_file_id = $uploadedFile->id();
		$printResult->generateAccessKey();
		$printResult->setReadyToPrint($this->canPrintFile($uploadedFile));
		$printResult->save();

		return $printResult;
	}

	/**
	 * @param \OpakeAdmin\Helper\Printing\Document[] $documents
	 * @return \Opake\Model\Document\PrintResult
	 * @throws \Exception
	 * @throws \iio\libmergepdf\Exception
	 */
	protected function compileMultipleDocuments($documents)
	{
		$app = Application::get();
		$pdfDocuments = [];
		foreach ($documents as $document) {
			if ($document->getContentMimeType() !== PrintCompiler::MIME_TYPE_PDF) {
				continue;
			}
			$pdfDocuments[] = $document;
		}

		if (!$pdfDocuments) {
			throw new \Exception('No one PDF document has been passed');
		}

		$merger = new Merger();

		$printPool = new PrintPool();
		$pdfDocuments = $printPool->runCompile($pdfDocuments);

		$tmpFiles = [];
		foreach ($pdfDocuments as $document) {
			if ($document instanceof FileDocument) {
				$merger->addFromFile($document->getFile()->getSystemPath());
			} else if ($document instanceof CompileDocument) {
				$tmpPath = $app->app_dir . '/_tmp/merge-' . uniqid();
				file_put_contents($tmpPath, $document->getContent());
				$merger->addFromFile($tmpPath);
				$tmpFiles[] = $tmpPath;
			} else {
				throw new \Exception('Unknown type of document');
			}
		}

		$outputContent = $merger->merge();

		foreach ($tmpFiles as $tmpPath) {
			if (is_file($tmpPath)) {
				unlink($tmpPath);
			}
		}

		$uploadedFile = $this->pixie->orm->get('UploadedFile');
		$uploadedFile->storeContent('print-result.pdf', $outputContent, [
			'is_protected' => true,
			'is_assigned' => true,
			'mime_type' => PrintCompiler::MIME_TYPE_PDF
		]);
		$uploadedFile->save();

		/** @var \Opake\Model\Document\PrintResult $printResult */
		$printResult = $this->pixie->orm->get('Document_PrintResult');
		$printResult->uploaded_file_id = $uploadedFile->id();
		$printResult->generateAccessKey();
		$printResult->setReadyToPrint(true);
		$printResult->save();

		if ($this->isCleanTemporaryFiles()) {
			$this->addUploadedFileToCleaningQueue($uploadedFile);
		}

		return $printResult;
	}

	/**
	 * @param \Opake\Model\UploadedFile $uploadedFile
	 * @return \Opake\Model\AbstractModel
	 */
	protected function addUploadedFileToCleaningQueue($uploadedFile)
	{
		$currentDate = new \DateTime();

		$model = $this->pixie->orm->get('Document_PrintResult_CleaningQueueRecord');
		$model->uploaded_file_id = $uploadedFile->id();
		$model->added_date = TimeFormat::formatToDBDatetime($currentDate);
		$model->save();

		return $model;
	}

	/**
	 * @param \Opake\Model\UploadedFile $file
	 * @return bool
	 */
	protected function canPrintFile($file)
	{
		$mimeTypesForPrint = [
			'image/jpeg',
			'image/png',
			'image/gif',
			'application/pdf'
		];

		return in_array($file->mime_type, $mimeTypesForPrint);
	}

}