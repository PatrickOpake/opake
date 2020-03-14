<?php

namespace OpakeAdmin\Helper\Printing\Document\Cases\Chart;

use iio\libmergepdf\Merger;
use Opake\Application;
use OpakeAdmin\Helper\Chart\DynamicFieldsHelper;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart;
use OpakeAdmin\Helper\Printing\Document\CompileDocument;
use OpakeAdmin\Helper\Printing\Document\PDFCompileDocument;
use OpakeAdmin\Helper\Printing\PrintCompiler;
use OpakeAdmin\Helper\Printing\Utils\Chart\ChartDynamicFieldsWriter;
use OpakeAdmin\Helper\Printing\Utils\Chart\ChartTemporaryFile;
use OpakeAdmin\Helper\Printing\Utils\Chart\HeadersWriter;

class MultipleChartFileWithHeader extends CompileDocument
{
	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var \Opake\Model\Forms\Document[]
	 */
	protected $forms;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 * @param \Opake\Model\Forms\Document[] $forms
	 */
	public function __construct($forms, $case = null)
	{
		$this->forms = $forms;
		$this->case = $case;
	}

	/**
	 * @return \Opake\Model\Cases\Item
	 */
	public function getCase()
	{
		return $this->case;
	}

	/**
	 * @return \Opake\Model\Forms\Document[]
	 */
	public function getForms()
	{
		return $this->forms;
	}

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return 'print-result.pdf';
	}

	/**
	 * @return string
	 */
	public function getContentMimeType()
	{
		return PrintCompiler::MIME_TYPE_PDF;
	}

	protected function compileContent()
	{

		$app = \Opake\Application::get();

		$files = [];
		foreach ($this->forms as $form) {
			$writer = new ChartDynamicFieldsWriter($form);
			if ($writer->hasDynamicFields()) {
				$tmpFile = new ChartTemporaryFile($form);
				$tmpFile->createFile();
				$writer->setCase($this->case);
				$writer->setInputFilePath($tmpFile->getFilePath());
				$writer->writeFields();
				$files[] = $tmpFile;
			} else {
				$filePath = $form->file->getSystemPath();
				if (is_file($filePath)) {
					$files[] = $filePath;
				}
			}
		}

		if (!$files) {
			throw new \Exception('Files list is empty');
		}

		$merger = new Merger();
		foreach ($files as $file) {
			if ($file instanceof ChartTemporaryFile) {
				$merger->addFromFile($file->getFilePath());
			} else {
				$merger->addFromFile($file);
			}
		}

		$outputContent = $merger->merge();

		foreach ($files as $file) {
			if ($file instanceof ChartTemporaryFile) {
				$file->cleanup();
			}
		}

		$tmpPath = $app->app_dir . '_tmp/multiple-chart-file-with-header-' . uniqid() . '.pdf';
		file_put_contents($tmpPath, $outputContent);

		$headersWriter = new HeadersWriter($tmpPath);
		$headersWriter->setOrganization($this->forms[0]->organization);
		if ($this->case) {
			$headersWriter->setCase($this->case);
		}
		$headersWriter->writeHeaders();

		$result = file_get_contents($tmpPath);
		unlink($tmpPath);

		return $result;
	}

}