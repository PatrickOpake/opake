<?php

namespace Opake\Helper\UploadedFile;

use Opake\Model\UploadedFile;

class UploadedFileHelper
{
	public static $extensionMimeTypes = [
		'image/jpeg' => 'jpg',
		'image/png' => 'png',
		'image/gif' => 'gif',
	    'application/vnd.ms-excel' => 'xls',
	    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
	    'text/csv' => 'csv'
	];

	public static $fileTypes = [
		'image/jpeg' => UploadedFile::FILE_TYPE_IMAGE,
		'image/png' => UploadedFile::FILE_TYPE_IMAGE,
		'image/gif' => UploadedFile::FILE_TYPE_IMAGE
	];

	/**
	 * @param string $mimeType
	 * @return string
	 * @throws \Exception
	 */
	public static function getExtensionByMimeType($mimeType)
	{
		if (isset(static::$extensionMimeTypes[$mimeType])) {
			return static::$extensionMimeTypes[$mimeType];
		}

		return null;
	}

	/**
	 * @param string $mimeType
	 * @return string
	 * @throws \Exception
	 */
	public static function getFileTypeByMimeType($mimeType)
	{
		if (isset(static::$fileTypes[$mimeType])) {
			return static::$fileTypes[$mimeType];
		}

		return null;
	}

	/**
	 * @param $path
	 * @return string
	 */
	public static function getFileMimeType($path)
	{
		if (function_exists('finfo_open')) {
			$info = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($info, $path);
			finfo_close($info);
			return $mime;
		} else if (function_exists('mime_content_type')) {
			return mime_content_type($path);
		}

		return null;
	}

}