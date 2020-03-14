<?php

namespace OpakeAdmin\Helper\Printing\Document;

use Opake\Model\UploadedFile;
use OpakeAdmin\Helper\Printing\Document;
use OpakeAdmin\Helper\Printing\PrintCompiler;

abstract class PDFCompileDocument extends CompileDocument
{

	/**
	 * @var string
	 */
	protected $inputTemporaryFile;

	/**
	 * @var string
	 */
	protected $outputTemporaryFile;

	/**
	 * @return int
	 */
	public function getType()
	{
		return Document::TYPE_PDF_COMPILE_DOCUMENT;
	}

	/**
	 * @return string
	 */
	public function getContentMimeType()
	{
		return PrintCompiler::MIME_TYPE_PDF;
	}

	public function generateFiles()
	{
		$app = \Opake\Application::get();
		$appDir = rtrim($app->app_dir, '/');
		$operationId =  uniqid();
		$this->inputTemporaryFile = $appDir . DIRECTORY_SEPARATOR . '_tmp' . DIRECTORY_SEPARATOR . 'pdf-'. $operationId . '-in.html' ;
		$this->outputTemporaryFile = $appDir . DIRECTORY_SEPARATOR . '_tmp' . DIRECTORY_SEPARATOR . 'pdf-' . $operationId . '-out.pdf';

		file_put_contents($this->inputTemporaryFile, $this->generateView()->render());
	}

	public function getPDFCompileCommand()
	{
		if (!$this->inputTemporaryFile || !$this->outputTemporaryFile) {
			throw new \Exception('Temporary files weren\'t generated');
		}

		$app = \Opake\Application::get();
		$options = $this->getPDFCompileOptions();
		$parameters = '-q --print-media-type ';
		if (!empty($options['landscape'])) {
			$parameters .= ' -O landscape';
		}
		if (!empty($options['page_size'])) {
			$parameters .= ' --page-size ' . $options['page_size'];
		}
		if (!empty($options['margins'])) {
			$parameters .= ' ' . $options['margins'];
		}

		$cmdString = '"' . $app->config->get('app.export.pdf') . '" ' . $parameters .
			' "' . $this->inputTemporaryFile . '" "' . $this->outputTemporaryFile . '"';

		$command = new \rikanishu\multiprocess\Command($cmdString, [
			\rikanishu\multiprocess\Command::OPTION_PROC => [
				'bypass_shell' => true
			]
		]);
		return $command;
	}

	public function cleanup()
	{
		if (is_file($this->inputTemporaryFile)) {
			unlink($this->inputTemporaryFile);
		}
		if (is_file($this->outputTemporaryFile)) {
			unlink($this->outputTemporaryFile);
		}
	}

	public function loadContent()
	{
		if (!is_file($this->outputTemporaryFile)) {
			throw new \Exception('Output file hasn\'t been created');
		}

		$this->prepareAfterCompiling();

		$this->content = file_get_contents($this->outputTemporaryFile);
	}

	protected function getPDFCompileOptions()
	{
		return [];
	}

	public function runCompile()
	{
		try {
			$this->generateFiles();
			$result = $this->getPDFCompileCommand()->runBlocking();

			if ($errors = $result->getStderr()) {
				throw new \Exception('PDF generation failed: ' . $errors);
			}

			$this->loadContent();
			$this->cleanup();

		} catch (\Exception $e) {
			$this->cleanup();
			throw $e;
		}
	}

	protected function compileContent()
	{

	}

	protected function prepareAfterCompiling()
	{

	}

	/**
	 * @return \Opake\View\View mixed
	 */
	abstract protected function generateView();
}