<?php

namespace Opake\Model\Patient;

use Opake\Model\AbstractModel;

class Chart extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'patient_chart';
	protected $_row = [
		'id' => null,
		'patient_id' => null,
		'name' => '',
		'uploaded_file_id' => null,
		'remote_file_id' => null,
		'uploaded_date' => null
	];
	protected $belongs_to = [
		'file' => [
			'model' => 'UploadedFile',
			'key' => 'uploaded_file_id'
		],
		'remote_file' => [
			'model' => 'RemoteStorageDocument',
			'key' => 'remote_file_id'
		]
	];

	public function save()
	{
		$this->uploaded_date = strftime('%Y-%m-%d %H:%M:%S');

		parent::save();
	}

	public function toArray()
	{
		return [
			'id' => (int) $this->id,
			'patient_id' => (int) $this->patient_id,
			'uploaded_file_id' => (int) $this->uploaded_file_id,
			'remote_file_id' => (int) $this->remote_file_id,
			'name' => $this->name,
			'uploaded_date' => date('D M d Y H:i:s O', strtotime($this->uploaded_date)),
			'url' => ($this->file && $this->file->loaded()) ? $this->file->getWebPath() : null,
			'mime_type' => $this->file->mime_type,
			'file_name' => $this->file->name,
		];
	}

}
