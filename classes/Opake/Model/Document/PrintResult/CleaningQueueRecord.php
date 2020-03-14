<?php

namespace Opake\Model\Document\PrintResult;

use Opake\Model\AbstractModel;

class CleaningQueueRecord extends AbstractModel
{
	public $id_field = 'id';

	public $table = 'documents_print_results_cleaning_queue';

	protected $_row = [
		'id' => null,
		'uploaded_file_id' => null,
		'remote_file_id' => null,
		'is_removed' => 0,
		'added_date' => null
	];

	protected $belongs_to = [
		'file' => [
			'model' => 'UploadedFile',
			'key' => 'uploaded_file_id',
			'cascade_delete' => false
		],
		'remote_file' => [
			'model' => 'RemoteStorageDocument',
			'key' => 'remote_file_id',
			'cascade_delete' => false
		]
	];

	public function removeFiles()
	{
		if (!$this->is_removed) {

			if ($this->uploaded_file_id) {
				if ($this->file && $this->file->loaded()) {
					$this->file->removeFile();
					$this->file->delete();
				}
			}

			$this->is_removed = 1;
			$this->save();
		}
	}
}