<?php

namespace Opake\Model\Forms\PDF;

use Opake\Model\AbstractModel;

class DynamicField extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'forms_document_pdf_dynamic_field';
	protected $_row = [
		'id' => null,
		'doc_id' => null,
		'page' => null,
		'name' => '',
		'x' => null,
		'y' => null,
		'width' => null,
		'height' => null
	];
	protected $belongs_to = [
		'document' => [
			'model' => 'Forms_Document',
			'key' => 'doc_id'
		]
	];
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\Chart\PDF\DynamicFieldFormatter'
	];

}
