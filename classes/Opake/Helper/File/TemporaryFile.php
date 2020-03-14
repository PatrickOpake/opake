<?php

namespace Opake\Helper\File;

use Opake\Helper\UploadedFile\UploadedFileHelper;
use Opake\Request\RequestUploadedFile;

class TemporaryFile
{
	/**
	 * @var string
	 */
	protected $originalFilePath;

	/**
	 * @var string
	 */
	protected $temporaryFilePath;

	/**
	 * @var string
	 */
	protected $mimeType;

	/**
	 * @var string
	 */
	protected $extension;

	/**
	 * @var string
	 */
	protected $temporaryFilePrefix = 'temp-';

	/**
	 * @param RequestUploadedFile|string $file
	 * @throws \Exception
	 */
	public function __construct($file)
	{
		if ($file instanceof RequestUploadedFile) {
			$this->originalFilePath = $file->getTmpName();
			$this->mimeType = $file->getType();
			$this->isFileFromRequest = true;
		} else if (is_string($file)) {
			$this->originalFilePath = $file;
		} else {
			throw new \Exception('Unknown file type');
		}
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getFilePath()
	{
		if (!$this->temporaryFilePath) {
			throw new \Exception('Temporary file has not been created');
		}
		return $this->temporaryFilePath;
	}


	/**
	 * @param string $mimeType
	 */
	public function setMimeType($mimeType)
	{
		$this->mimeType = $mimeType;
	}

	/**
	 * @param string $extension
	 */
	public function setExtension($extension)
	{
		$this->extension = $extension;
	}

	/**
	 * @param string $temporaryFilePrefix
	 */
	public function setTemporaryFilePrefix($temporaryFilePrefix)
	{
		$this->temporaryFilePrefix = $temporaryFilePrefix;
	}

	public function create()
	{
		$this->cleanup();

		$app = \Opake\Application::get();
		$extension = null;
		if ($this->extension) {
			$extension = $this->extension;
		} else if ($this->mimeType) {
			$extension = UploadedFileHelper::getExtensionByMimeType($this->mimeType);
		}
		if (!$extension) {
			$extension = pathinfo($this->originalFilePath, PATHINFO_EXTENSION);
		}
		$tmpPath = $app->app_dir . '_tmp/' . $this->temporaryFilePrefix  . uniqid();
		if ($extension) {
			$tmpPath .= '.' . $extension;
		}
		$this->temporaryFilePath = $tmpPath;
		if (!copy($this->originalFilePath, $this->temporaryFilePath)) {
			throw new \Exception('Cannot copy an original file');
		}
	}

	public function cleanup()
	{
		if ($this->temporaryFilePath && file_exists($this->temporaryFilePath)) {
			unlink($this->temporaryFilePath);
		}
	}

	public function __destruct()
	{
		$this->cleanup();
	}
}