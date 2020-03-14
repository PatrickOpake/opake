<?php

namespace Opake\Model;

use Opake\Helper\Config;
use Opake\Helper\TimeFormat;
use Opake\Helper\UploadedFile\UploadedFileHelper;
use Opake\Request\RequestUploadedFile;

class UploadedFile extends AbstractModel
{

	const FILE_TYPE_IMAGE = 'image';

	public $id_field = 'id';
	public $table = 'uploaded_files';
	protected $_row = [
		'id' => null,
		'original_filename' => null,
		'path' => null,
		'name' => null,
		'extension' => null,
		'mime_type' => null,
		'system' => null,
		'assigned' => null,
		'protected' => null,
		'protected_type' => null,
	    'uploaded_date' => null
	];

	public function save()
	{
		if (!$this->loaded()) {
			$this->uploaded_date = TimeFormat::formatToDBDatetime(new \DateTime());
		}

		parent::save();
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getWebPath()
	{
		if ($this->protected) {
			return $this->buildProtectedFileUrl();
		}

		$webPath = '';
		if (!$this->system) {
			$webPath = static::getUploadsWebPath();
		}
		$webPath = $webPath . $this->path . $this->name;
		if ($this->extension) {
			$webPath .= '.' . $this->extension;
		}

		return $webPath;
	}

	/**
	 * @return string
	 */
	public function getSystemPath()
	{
		if ($this->system) {
			$rootFolder = $this->pixie->root_dir;
		} else if ($this->protected) {
			$rootFolder = static::getProtectedSystemPath();
		} else {
			$rootFolder = static::getUploadsSystemPath();
		}

		$systemPath = rtrim($rootFolder, '/') . $this->path . $this->name;
		if ($this->extension) {
			$systemPath .= '.' . $this->extension;
		}

		return $systemPath;
	}

	/**
	 * @return bool
	 */
	public function isFileExists()
	{
		return file_exists($this->getSystemPath());
	}

	/**
	 * @return string
	 */
	public function readContent()
	{
		return file_get_contents($this->getSystemPath());
	}

	/**
	 * @return int
	 */
	public function getSize()
	{
		return filesize($this->getSystemPath());
	}

	public function removeFile()
	{
		$path = $this->getSystemPath();
		if (is_file($path)) {
			unlink($path);
		}
	}

	/**
	 * @param RequestUploadedFile $upload
	 * @param array $params
	 * @return UploadedFile
	 * @throws \Exception
	 */
	public function storeFile(RequestUploadedFile $upload, $params = [])
	{
		if ($this->loaded()) {
			throw new \Exception('Model already saved');
		}

		if ($upload->isEmpty()) {
			throw new \Exception('Trying to upload non-loaded file');
		}

		if ($upload->hasErrors()) {
			throw new \Exception('Error while file uploading, fileinfo has error ' . $upload->getError());
		}

		if (isset($params['allowed_size']) && $upload->getSize() > $params['allowed_size']) {
			throw new \Exception('Exceeded filesize limit');
		}

		$newFileName = md5(uniqid('', true));
		$firstPart = substr($newFileName, 0, 2);
		$secondPart = substr($newFileName, 0, 4);

		$path = '/' . $firstPart . '/' . $secondPart . '/';

		if (!empty($params['is_protected'])) {
			$uploadDir = static::getProtectedSystemPath();
		} else {
			$uploadDir = static::getUploadsSystemPath();
		}

		$absoluteDirPath = rtrim($uploadDir, '/') . $path;
		if (!is_dir($absoluteDirPath)) {
			mkdir($absoluteDirPath, 0755, true);
		}
		$mimeType = UploadedFileHelper::getFileMimeType($upload->getTmpName());
		if ($mimeType === null && $upload->getType()) {
			$mimeType = $upload->getType();
		}
		if (!$mimeType) {
			throw new \Exception('Unknown file type');
		}
		if (isset($params['allowed_mime_types']) && !in_array($mimeType, $params['allowed_mime_types'])) {
			throw new \Exception('Unexpected file type');
		}

		$extension = UploadedFileHelper::getExtensionByMimeType($mimeType);
		if ($extension === null) {
			if (strpos($upload->getName(), '.') !== false) {
				$parts = explode('.', $upload->getName());
				$extension = $parts[count($parts) - 1];
			}
		}

		if (!empty($params['use_original_file_name']) && $upload->getName()) {
			$originalFileName = $upload->getName();
			if (strpos($originalFileName, '.') !== false) {
				$originalFileName = preg_replace('/\.[^.]+$/', '', $originalFileName);
			}
			$originalFileName = preg_replace('/[\s\/]/', '_', $originalFileName);
			$fileName = $originalFileName;
		} else {
			$fileName = $newFileName;
		}
		$absoluteFilePath = static::makeAbsolutePath($absoluteDirPath, $fileName, $extension);
		if (file_exists($absoluteFilePath)) {
			$fileName .= '_' . $newFileName;
			$absoluteFilePath = static::makeAbsolutePath($absoluteDirPath, $fileName, $extension);
		}
		if (!$upload->save($absoluteFilePath)) {
			throw new \Exception('Error while saving uploaded file');
		}

		$this->path = $path;
		$this->original_filename = $upload->getName();
		$this->extension = $extension;
		$this->name = $fileName;
		$this->mime_type = $mimeType;
		$this->system = false;
		$this->protected = !empty($params['is_protected']);
		$this->protected_type = (!empty($params['protected_type'])) ? $params['protected_type'] : null;
		$this->assigned = !empty($params['is_assigned']);

		$this->prepareFile();

		return $this;
	}

	/**
	 * @param string $localPath
	 * @param array $params
	 * @return $this
	 * @throws \Exception
	 */
	public function storeLocalFile($localPath, $params = [])
	{
		if ($this->loaded()) {
			throw new \Exception('Model already saved');
		}

		if (!is_file($localPath)) {
			throw new \Exception('Target file doesn\'t exist');
		}

		$newFileName = md5(uniqid('', true));
		$firstPart = substr($newFileName, 0, 2);
		$secondPart = substr($newFileName, 0, 4);

		$path = '/' . $firstPart . '/' . $secondPart . '/';

		if (!empty($params['is_protected'])) {
			$uploadDir = static::getProtectedSystemPath();
		} else {
			$uploadDir = static::getUploadsSystemPath();
		}

		$absoluteDirPath = rtrim($uploadDir, '/') . $path;
		if (!is_dir($absoluteDirPath)) {
			mkdir($absoluteDirPath, 0755, true);
		}
		$mimeType = UploadedFileHelper::getFileMimeType($localPath);
		if ($mimeType === null && isset($params['mime_type'])) {
			$mimeType = $params['mime_type'];
		}
		if (!$mimeType) {
			throw new \Exception('Unknown file type');
		}

		$extension = UploadedFileHelper::getExtensionByMimeType($mimeType);
		if ($extension === null) {
			if (strpos($localPath, '.') !== false) {
				$parts = explode('.', $localPath);
				$extension = $parts[count($parts) - 1];
			}
		}

		if (!empty($params['use_original_file_name'])) {
			$originalFileName = basename($localPath);
			if (strpos($originalFileName, '.') !== false) {
				$originalFileName = preg_replace('/\.[^.]+$/', '', $originalFileName);
			}
			$originalFileName = preg_replace('/[\s\/]/', '_', $originalFileName);
			$fileName = $originalFileName;
		} else {
			$fileName = $newFileName;
		}
		$absoluteFilePath = static::makeAbsolutePath($absoluteDirPath, $fileName, $extension);
		if (file_exists($absoluteFilePath)) {
			$fileName .= '_' . $newFileName;
			$absoluteFilePath = static::makeAbsolutePath($absoluteDirPath, $fileName, $extension);
		}
		if (!copy($localPath, $absoluteFilePath)) {
			throw new \Exception('Error while saving uploaded file');
		}

		$this->path = $path;
		$this->original_filename = basename($localPath);
		$this->extension = $extension;
		$this->name = $fileName;
		$this->mime_type = $mimeType;
		$this->system = !empty($params['is_system']);
		$this->protected = !empty($params['is_protected']);
		$this->protected_type = (!empty($params['protected_type'])) ? $params['protected_type'] : null;
		$this->assigned = (!isset($params['is_assigned']) || !empty($params['is_assigned']));

		$this->prepareFile();

		return $this;
	}

	/**
	 * @param string $contentFilename
	 * @param string $content
	 * @param array $params
	 * @return $this
	 * @throws \Exception
	 */
	public function storeContent($contentFilename, $content, $params = [])
	{
		if ($this->loaded()) {
			throw new \Exception('Model already saved');
		}

		$newFileName = md5(uniqid('', true));
		$firstPart = substr($newFileName, 0, 2);
		$secondPart = substr($newFileName, 0, 4);

		$path = '/' . $firstPart . '/' . $secondPart . '/';

		if (!empty($params['is_protected'])) {
			$uploadDir = static::getProtectedSystemPath();
		} else {
			$uploadDir = static::getUploadsSystemPath();
		}

		$absoluteDirPath = rtrim($uploadDir, '/') . $path;
		if (!is_dir($absoluteDirPath)) {
			mkdir($absoluteDirPath, 0755, true);
		}

		if (!isset($params['mime_type'])) {
			throw new \Exception('Unknown file type');
		}
		$mimeType = $params['mime_type'];

		$extension = UploadedFileHelper::getExtensionByMimeType($mimeType);
		if ($extension === null) {
			if (strpos($contentFilename, '.') !== false) {
				$parts = explode('.', $contentFilename);
				$extension = $parts[count($parts) - 1];
			}
		}

		if (!empty($params['use_original_file_name'])) {
			$originalFileName = $contentFilename;
			if (strpos($originalFileName, '.') !== false) {
				$originalFileName = preg_replace('/\.[^.]+$/', '', $originalFileName);
			}
			$originalFileName = preg_replace('/[\s\/]/', '_', $originalFileName);
			$fileName = $originalFileName;
		} else {
			$fileName = $newFileName;
		}
		$absoluteFilePath = static::makeAbsolutePath($absoluteDirPath, $fileName, $extension);
		if (file_exists($absoluteFilePath)) {
			$fileName .= '_' . $newFileName;
			$absoluteFilePath = static::makeAbsolutePath($absoluteDirPath, $fileName, $extension);
		}

		file_put_contents($absoluteFilePath, $content);

		$this->path = $path;
		$this->original_filename = $contentFilename;
		$this->extension = $extension;
		$this->name = $fileName;
		$this->mime_type = $mimeType;
		$this->system = !empty($params['is_system']);
		$this->protected = !empty($params['is_protected']);
		$this->protected_type = (!empty($params['protected_type'])) ? $params['protected_type'] : null;
		$this->assigned = (!isset($params['is_assigned']) || !empty($params['is_assigned']));

		$this->prepareFile();

		return $this;
	}

	public function delete()
	{
		parent::delete();
		$this->removeFile();
	}

	protected function prepareFile()
	{

	}

	protected function buildProtectedFileUrl()
	{
		return $this->getProtectedFilesRoute() . '?id=' . $this->id();
	}

	/**
	 * @param string $dir
	 * @param string $fileName
	 * @param string $extension
	 * @return string
	 */
	protected static function makeAbsolutePath($dir, $fileName, $extension)
	{
		$absoluteFilePath = $dir . $fileName;
		if ($extension) {
			$absoluteFilePath .= '.' . $extension;
		}

		return $absoluteFilePath;
	}

	/**
	 * @return string
	 */
	public static function getUploadsWebPath()
	{
		return Config::get('app.files.public');
	}

	/**
	 * @return string
	 */
	public static function getProtectedSystemPath()
	{
		return Config::get('app.protected_files.path');
	}

	/**
	 * @return string
	 */
	public static function getUploadsSystemPath()
	{
		return Config::get('app.share') . Config::get('app.files.path');
	}

	/**
	 * @return mixed
	 */
	public static function getProtectedFilesRoute()
	{
		return Config::get('app.protected_files.route');
	}

	/**
	 * @return bool
	 */
	public function isPDF()
	{
		return strtolower($this->extension) === 'pdf';
	}

}