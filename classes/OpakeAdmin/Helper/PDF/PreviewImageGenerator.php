<?php

namespace OpakeAdmin\Helper\PDF;

use Opake\Model\UploadedFile;

class PreviewImageGenerator
{
	/**
	 * @var \Opake\Model\UploadedFile
	 */
	protected $uploadedFile;

	/**
	 * @var int
	 */
	protected $page;

	/**
	 * @var array
	 */
	protected $convertParams = [];

	/**
	 * @param \Opake\Model\UploadedFile $uploadedFile
	 */
	public function __construct(UploadedFile $uploadedFile)
	{
		$this->uploadedFile = $uploadedFile;
	}

	/**
	 * @return int
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * @param int $page
	 */
	public function setPage($page)
	{
		$this->page = $page;
	}

	/**
	 * @return array
	 */
	public function getConvertParams()
	{
		return $this->convertParams;
	}

	/**
	 * @param array $convertParams
	 */
	public function setConvertParams($convertParams)
	{
		$this->convertParams = $convertParams;
	}

	public function generateImage()
	{
		$app = \Opake\Application::get();

		if (!$this->page) {
			throw new \Exception('Unknown page');
		}

		if (!$this->uploadedFile->loaded()) {
			throw new \Exception('File no found');
		}

		if ($this->uploadedFile->mime_type != 'application/pdf') {
			throw new \Exception('Unknown file format');
		}

		$outputPath = $this->getImagePath();

		if (is_file($outputPath)) {
			return true;
		}

		$pdfStorage = $this->getPdfStorageSystemPath();
		if (!is_dir($pdfStorage)) {
			mkdir($pdfStorage, 0755, true);
		}

		$convertParams = array_replace($this->getDefaultConvertParams(), $this->convertParams);
		$commandOptions = '';
		if (isset($convertParams['density'])) {
			$commandOptions .= '-density ' . $convertParams['density'] . ' ';
		}
		if (isset($convertParams['antialias']) && $convertParams['antialias']) {
			$commandOptions .= '-antialias ';
		}
		if (isset($convertParams['resize'])) {
			$commandOptions .= '-resize ' . $convertParams['resize'] . ' ';
		}
		if (isset($convertParams['quality'])) {
			$commandOptions .= '-quality ' . $convertParams['quality'] . ' ';
		}

		$convertCommand = $app->config->get('app.imagemagick_convert_command');

		$commandString =  $convertCommand . ' ' . $commandOptions .
			' "' . $this->uploadedFile->getSystemPath() . '[' . ($this->page - 1) . ']" "' . $outputPath . '"';

		$command = new \rikanishu\multiprocess\Command($commandString, [
			\rikanishu\multiprocess\Command::OPTION_PROC => [
				'bypass_shell' => true
			]
		]);
		$output = $command->runBlocking();
		if ($output->getStderr()) {
			throw new \Exception('Error while converting of PDF:' . $output->getStderr());
		}

		return true;
	}

	public function getImagePath()
	{
		if (!$this->page) {
			throw new \Exception('Unknown page');
		}

		return $this->getPdfStorageSystemPath() . '/' . $this->page . '.png';
	}

	public function clearImagesCache()
	{
		$pdfStoragePath = $this->getPdfStorageSystemPath();
		if (is_dir($pdfStoragePath)) {
			$this->removeDirectory($pdfStoragePath);
		}
	}

	protected function getPdfStorageSystemPath()
	{
		return $this->getStorageSystemPath() . '/' . $this->uploadedFile->id();
	}

	protected function getStorageSystemPath()
	{
		$app = \Opake\Application::get();
		return $app->app_dir . '/_tmp/pdf-image-preview';
	}

	protected function removeDirectory($path)
	{
		$dir = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $file) {
			if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
		rmdir($path);
	}

	protected function getDefaultConvertParams()
	{
		return [
			'density' => 150,
		    'antialias' => true,
		    'resize' => '1024x',
		    'quality' => 100
		];
	}

}