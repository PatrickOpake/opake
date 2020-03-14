<?php

namespace Opake\Model\Document;

use Opake\Model\AbstractModel;

class PrintResult extends AbstractModel
{
	public $id_field = 'id';

	public $table = 'documents_print_results';

	protected $_row = [
		'id' => null,
		'key' => null,
		'uploaded_file_id' => null,
	];

	protected $belongs_to = [
		'file' => [
			'model' => 'UploadedFile',
			'key' => 'uploaded_file_id',
			'cascade_delete' => false
		],
	];

	/**
	 * @var bool
	 */
	protected $readyToPrint = true;

	/**
	 * @return boolean
	 */
	public function isReadyToPrint()
	{
		return $this->readyToPrint;
	}

	/**
	 * @param boolean $readyToPrint
	 */
	public function setReadyToPrint($readyToPrint)
	{
		$this->readyToPrint = $readyToPrint;
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getResultUrl()
	{
		if ($this->isReadyToPrint()) {
			$url = '/document/printResult/' . $this->id();
			$url .= '?key=' . $this->key;
			return $url;
		} else if ($this->file && $this->file->loaded()) {
			return $this->file->getWebPath();
		}

		throw new \Exception('Unknown print result URL');
	}

	/**
	 * @return string
	 */
	public function generateAccessKey()
	{
		$this->key = md5(uniqid());

		return $this->key;
	}
}