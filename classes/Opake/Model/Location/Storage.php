<?php

namespace Opake\Model\Location;
use Opake\Model\AbstractModel;

/**
 * @property \Opake\Model\Organization $organization Organization who placed on site
 */
class Storage extends AbstractModel
{


	public $id_field = 'id';
	public $table = 'location_storage';
	protected $_row = array(
		'id' => null,
		'site_id' => null,
		'name' => '',
	);

	protected $belongs_to = [
		'site' => [
			'model' => 'Site',
			'key' => 'site_id',
		],
	];

	protected $has_many = [
		'packs' => [
			'model' => 'Inventory_Pack',
			'key' => 'location_id',
			'cascade_delete' => true
		],
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('name')->rule('filled')->error('Invalid Name');
		$validator->field('name')->rule('callback', function ($val, $validator, $field) {
			$model = $this->pixie->orm->get('Location_Storage')->where('name', $this->name)->where('site_id', $this->site_id)->find();
			return !($model->loaded() && $this->id !== $model->id);
		})->error(sprintf('Location Storage with name %s already exists', $this->name));
		return $validator;
	}

	public function toArray()
	{
		return [
			'id' => (int)$this->id,
			'site_id' => (int)$this->site_id,
			'site_name' => $this->site->name,
			'name' => $this->name,
		];
	}

}
