<?php

namespace Opake\Model\Cases\Registration;

use Opake\Model\AbstractModel;

class Document extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'case_registration_documents';
	protected $_row = [
		'id' => null,
		'case_registration_id' => null,
		'document_type' => null,
		'uploaded_file_id' => null,
		'remote_file_id' => null,
		'uploaded_date' => null,
		'status' => null
	];

	protected $belongs_to = [
		'file' => [
			'model' => 'UploadedFile',
			'key' => 'uploaded_file_id'
		],
		'case_registration' => [
			'model' => 'Cases_Registration',
			'key' => 'case_registration_id'
		],
		'type' => [
			'model' => 'Cases_Registration_Document_Type',
			'key' => 'document_type'
		],
		'remote_file' => [
			'model' => 'RemoteStorageDocument',
			'key' => 'remote_file_id'
		]
	];

	public function toArray()
	{
		$data = [
			'id' => $this->id(),
			'status' => (int)$this->status,
			'type' => $this->type->id(),
			'name' => $this->type->name,
			'url' => ($this->file && $this->file->loaded()) ? $this->file->getWebPath() : null,
			'uploaded_date' => $this->uploaded_date,
			'mime_type' => $this->file->mime_type,
			'dos' => $this->case_registration->case->time_start,
			'procedure' => $this->case_registration->case->type->toArray(),
		];
		return $data;
	}

}
