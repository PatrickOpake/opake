<?php

namespace Opake\Model\UploadedFile\Image;

class Info extends \Opake\Model\AbstractModel
{
	public $id_field = 'id';
	public $table = 'uploaded_files_image_info';
	protected $_row = [
		'id' => null,
		'uploaded_file_id' => null,
		'settings_type' => null,
	];

	protected $belongs_to = [
		'image' => [
			'model' => 'UploadedFile_Image',
			'key' => 'uploaded_file_id'
		]
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];
}