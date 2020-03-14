<?php

namespace Opake\Model\Cases;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

class Discharge extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'case_discharge';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'uploaded_file_id' => null,
		'uploaded' => null
	];
	protected $belongs_to = [
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		],
		'file' => [
			'model' => 'UploadedFile',
			'key' => 'uploaded_file_id'
		]
	];

	protected function deleteInternal()
	{
		parent::deleteInternal();

		if ($this->file->loaded()) {
			$this->file->removeFile();
		}
	}

	public function toArray()
	{
		return [
			'id' => $this->id,
			'name' => $this->file->original_filename,
			'path' => $this->file->getWebPath(),
			'uploaded' => $this->uploaded
		];
	}

	public function save()
	{
		$this->uploaded = TimeFormat::formatToDBDatetime(new \DateTime());

		parent::save();
	}
}
