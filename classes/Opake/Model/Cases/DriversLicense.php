<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;

class DriversLicense extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'case_drivers_license';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'uploaded_file_id' => null,
		'uploaded_date' => null
	];

	protected $belongs_to = [
		'file' => [
			'model' => 'UploadedFile',
			'key' => 'uploaded_file_id'
		],
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		]
	];

	public function toArray()
	{
		$data = [
			'id' => $this->id,
			'url' => ($this->file && $this->file->loaded()) ? $this->file->getWebPath() : null,
			'uploaded_date' => $this->uploaded_date,
			'mime_type' => $this->file->mime_type,
		];

		return $data;
	}

}
