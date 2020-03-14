<?php

namespace Opake\Model\Inventory;

use Opake\Model\AbstractModel;
use Opake\Helper\Config;

class Type extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'inventory_type';
	protected $_row = [
		'id' => null,
		'name' => '',
		'image_id' => null,
	];

	protected $belongs_to = [
		'image' => [
			'model' => 'UploadedFile_Image',
			'key' => 'image_id'
		]
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('name')->rule('filled')->rule('min_length', 2)->error('Invalid Name');
		$validator->field('name')->rule('unique', $this)->error(sprintf('Item Type with name %s already exists', $this->name));
		return $validator;
	}

	public function getImage($size = NULL)
	{
		if ($image = $this->getImageModel()) {
			return $image->getThumbnailWebPath($size);
		}

		return '';
	}

	/**
	 * @return \Opake\Model\UploadedFile\Image
	 */
	protected function getImageModel()
	{
		if ($this->image_id) {
			if ($this->image->loaded()) {
				return $this->image;
			}
			if (!$this->image->loaded() && $this->image_id) {
				$model = $this->pixie->orm->get('UploadedFile_Image', $this->image_id);
				if ($model->loaded()) {
					return $model;
				}
			}
		}

		return null;
	}

}
