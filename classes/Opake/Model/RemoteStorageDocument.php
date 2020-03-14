<?php

namespace Opake\Model;

class RemoteStorageDocument extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'remote_storage_document';

	protected $_row = [
		'id' => null,
		'filename' => null,
		'content_item_id' => null,
		'asset_id' => null
	];


	public function deleteContentItem()
	{
		if ($this->content_item_id) {
			$remoteFileService = new \OpakeAdmin\Helper\RemoteDocument\Service($this->pixie);
			$remoteFileService->deleteContentItem($this->content_item_id);
		}
	}
}