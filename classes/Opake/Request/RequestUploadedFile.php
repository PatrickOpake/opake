<?php

namespace Opake\Request;

class RequestUploadedFile
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $tmpName;

	/**
	 * @var int
	 */
	protected $error;

	/**
	 * @var int
	 */
	protected $size;

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getTmpName()
	{
		return $this->tmpName;
	}

	/**
	 * @param string $tmpName
	 */
	public function setTmpName($tmpName)
	{
		$this->tmpName = $tmpName;
	}

	/**
	 * @return int
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param int $error
	 */
	public function setError($error)
	{
		$this->error = $error;
	}

	/**
	 * @return int
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @param int $size
	 */
	public function setSize($size)
	{
		$this->size = $size;
	}

	/**
	 * @param $paramName
	 * @param $value
	 */
	public function setParam($paramName, $value)
	{
		switch ($paramName) {
			case 'name':
				$this->setName($value);
				break;
			case 'type':
				$this->setType($value);
				break;
			case 'tmp_name':
				$this->setTmpName($value);
				break;
			case 'error':
				$this->setError($value);
				break;
			case 'size':
				$this->setSize($value);
				break;
		}
	}

	/**
	 * @return bool
	 */
	public function isEmpty()
	{
		return ($this->error === UPLOAD_ERR_NO_FILE);
	}

	/**
	 * @return bool
	 */
	public function hasErrors()
	{
		return ($this->error !== UPLOAD_ERR_OK);
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function save($path)
	{
		return move_uploaded_file($this->tmpName, $path);
	}


	/**
	 * @return array
	 */
	public static function getRequestFiles()
	{
		return static::makeRequestFiles($_FILES);
	}

	/**
	 * @param array $files
	 * @return array
	 */
	protected static function makeRequestFiles($files)
	{
		$arrayForFill = [];
		foreach ($files as $firstNameKey => $arFileDescriptions) {
			foreach ($arFileDescriptions as $fileDescriptionParam => $mixedValue) {
				static::getFileHierarchyAndValue($arrayForFill,
					$firstNameKey,
					$files[$firstNameKey][$fileDescriptionParam],
					$fileDescriptionParam);
			}
		}
		return $arrayForFill;
	}

	protected static function getFileHierarchyAndValue(&$arrayForFill, $currentKey, $currentMixedValue, $fileDescriptionParam)
	{
		if (is_array($currentMixedValue)) {
			foreach ($currentMixedValue as $nameKey => $mixedValue) {
				static::getFileHierarchyAndValue($arrayForFill[$currentKey],
					$nameKey,
					$mixedValue,
					$fileDescriptionParam);
			}
		} else {
			if (!isset($arrayForFill[$currentKey])) {
				$arrayForFill[$currentKey] = new static();
			}
			$upload = $arrayForFill[$currentKey];
			$upload->setParam($fileDescriptionParam, $currentMixedValue);
		}
	}

	public static function fillModel($data)
	{

		$upload = new static();
		$upload->setName($data['name']);
		$upload->setType($data['type']);
		$upload->setTmpName($data['tmp_name']);
		$upload->setError($data['error']);
		$upload->setSize($data['size']);

		return $upload;
	}
}