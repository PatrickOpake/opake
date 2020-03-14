<?php

namespace Opake\Model\UploadedFile;

use Opake\Request\RequestUploadedFile;

class Image extends \Opake\Model\UploadedFile
{
	/**
	 * @var string
	 */
	protected $imageSettingsType;

	/**
	 * @var array
	 */
	protected $imageSettings;

	/**
	 * @var bool
	 */
	protected $isSettingsInitialized;

	/**
	 * @param \PHPixie\Pixie $pixie
	 */
	public function __construct($pixie)
	{
		$this->has_one['image_info'] = [
			'model' => 'UploadedFile_Image_Info',
			'key' => 'uploaded_file_id',
			'cascade_delete' => true
		];

		parent::__construct($pixie);
	}

	/**
	 * @param string|array $settings
	 * @return mixed
	 */
	public function initImageSettings($settings = null)
	{
		if (is_array($settings)) {
			$this->imageSettingsType = null;
			$this->imageSettings = $settings;
		} else {
			if (!is_string($settings)) {
				if ($this->imageSettingsType) {
					$settings = $this->imageSettingsType;
				} else if ($this->image_info && $this->image_info->loaded()) {
					$settings = $this->image_info->settings_type;
				}
			}

			$settingName = $settings;
			$typesSettings = $this->pixie->config->get('image.settings');
			if (isset($typesSettings[$settingName])) {
				$this->imageSettings = $typesSettings[$settingName];
			} else {
				$this->imageSettings = $this->pixie->config->get('image.default_settings');
			}

			$this->imageSettingsType = $settings;
		}
		$this->isSettingsInitialized = true;
	}

	/**
	 * @return array
	 */
	public function getImageSettings()
	{
		if (!$this->isSettingsInitialized) {
			$this->initImageSettings();
		}

		return $this->imageSettings;
	}


	/**
	 * @param string $thumbnailName
	 * @return string
	 */
	public function getThumbnailWebPath($thumbnailName)
	{
		if ($this->protected) {
			throw new \Exception('This file is protected, can\'t be available via public path');
		}

		if (!$thumbnailName) {
			return $this->getWebPath();
		}

		$webPath = '';
		if (!$this->system) {
			$webPath = static::getUploadsWebPath();
		}
		$webPath = $webPath . $this->path . $this->name . '_' . $thumbnailName;
		if ($this->extension) {
			$webPath .= '.' . $this->extension;
		}

		return $webPath;
	}

	/**
	 * @param RequestUploadedFile $upload
	 * @param array $params
	 * @return Image
	 * @throws \Exception
	 */
	public function storeFile(RequestUploadedFile $upload, $params = [])
	{
		if (!$params) {
			$params = $this->getImageSettings();
		}

		return parent::storeFile($upload, $params);
	}

	/**
	 * @param string $localPath
	 * @param array $params
	 * @return $this
	 * @throws \Exception
	 */
	public function storeLocalFile($localPath, $params = [])
	{
		if (!$params) {
			$params = $this->getImageSettings();
		}

		return parent::storeLocalFile($localPath, $params);
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
		if (!$params) {
			$params = $this->getImageSettings();
		}

		return parent::storeContent($contentFilename, $content, $params);
	}

	protected function prepareFile()
	{
		$imgUtils = new \Opake\Image\ImageUtils($this->pixie);
		$imgUtils->setExifOrientation($this->getSystemPath());
	}

	/**
	 * @param string $thumbnailName
	 * @return string
	 */
	public function getThumbnailSystemPath($thumbnailName)
	{
		if (!$thumbnailName) {
			return $this->getSystemPath();
		}

		if ($this->system) {
			$rootFolder = $this->pixie->root_dir;
		} else if ($this->protected) {
			$rootFolder = static::getProtectedSystemPath();
		} else {
			$rootFolder = static::getUploadsSystemPath();
		}

		$systemPath = rtrim($rootFolder, '/') . $this->path . $this->name . '_' . $thumbnailName;
		if ($this->extension) {
			$systemPath .= '.' . $this->extension;
		}

		return $systemPath;
	}

	public function createThumbnails()
	{
		$settings = $this->getImageSettings();
		if (isset($settings['thumbnails'])) {
			foreach ($settings['thumbnails'] as $thumbnailName => $conf) {
				if (!empty($conf['width']) && !empty($conf['height'])) {

					$accuracy = (!empty($conf['accuracy'])) ? $conf['accuracy'] : false;

					$imgUtils = new \Opake\Image\ImageUtils($this->pixie);
					$imgDriver = $imgUtils->read($this->getSystemPath());
					$newImgDriver = $imgUtils->resize($imgDriver, (int)$conf['width'], (int)$conf['height'], $accuracy);

					$newImgDriver->save($this->getThumbnailSystemPath($thumbnailName));
				}
			}
		}
	}

	public function save()
	{
		parent::save();

		if ($this->loaded()) {
			if (!$this->image_info || !$this->image_info->loaded()) {
				$imageInfoModel = $this->pixie->orm->get('UploadedFile_Image_Info');
				$imageInfoModel->uploaded_file_id = $this->id();
				$imageInfoModel->settings_type = $this->imageSettingsType;
				$imageInfoModel->save();
			}
		}
	}

	public function removeFile()
	{
		parent::removeFile();
		$currentSettings = $this->getImageSettings();
		if (!empty($currentSettings['thumbnails'])) {
			foreach ($currentSettings['thumbnails'] as $name => $conf) {
				$thumbnailPath = $this->getThumbnailSystemPath($name);
				if (is_file($thumbnailPath)) {
					unlink($thumbnailPath);
				}
			}
		}
	}
}