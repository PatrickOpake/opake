<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;

class Chart extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'case_chart';
	protected $_row = [
		'id' => null,
		'list_id' => null,
		'name' => '',
		'uploaded_file_id' => null,
		'remote_file_id' => null,
		'uploaded_date' => null,
		'is_booking_sheet' => 0
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

	public function toArray()
	{
		return [
			'id' => (int) $this->id,
			'list_id' => (int) $this->list_id,
			'case_id' => $this->getCaseId(),
			'booking_id' => $this->getBookingId(),
			'uploaded_file_id' => (int) $this->uploaded_file_id,
			'remote_file_id' => (int) $this->remote_file_id,
			'name' => $this->name,
			'uploaded_date' => date('D M d Y H:i:s O', strtotime($this->uploaded_date)),
			'url' => ($this->file && $this->file->loaded()) ? $this->file->getWebPath() : null,
			'mime_type' => $this->file->mime_type,
			'file_name' => $this->file->original_filename,
			'is_booking_sheet' => (bool) $this->is_booking_sheet
		];
	}

	public function getCaseId()
	{
		$query = $this->pixie->db->query('select')
			->table('case_booking_list')
			->fields('case_id')
			->where(['id', $this->list_id])
			->execute()
			->current();

		if ($query) {
			return $query->case_id;
		} else {
			return false;
		}
	}

	public function getBookingId()
	{
		$query = $this->pixie->db->query('select')
			->table('case_booking_list')
			->fields('booking_id')
			->where(['id', $this->list_id])
			->execute()
			->current();

		if ($query) {
			return $query->booking_id;
		} else {
			return false;
		}
	}
}
