<?php

namespace Opake\Model;

/**
 * @property \Opake\Model\Organization $organization Organization who placed on site
 */
class Location extends AbstractModel
{

	const DEFAULT_CASE_COLOR = 'default-grey';

	public $id_field = 'id';
	public $table = 'location';
	protected $_row = array(
		'id' => null,
		'site_id' => null,
		'name' => '',
		'case_color' => self::DEFAULT_CASE_COLOR
	);

	protected $baseFormatter = [
		'class' => '\Opake\Formatter\Location\DefaultLocationFormatter'
	];

	protected $formatters = [
		'SelectOptions' => [
			'class' => '\Opake\Formatter\Location\SelectOptionsFormatter'
		],
		'CalendarSettings' => [
			'class' => '\Opake\Formatter\Location\CalendarSettingsFormatter'
		],
		'OverviewSettings' => [
			'class' => '\Opake\Formatter\Location\OverviewSettingsFormatter'
		]
	];

	protected $belongs_to = [
		'site' => [
			'model' => 'Site',
			'key' => 'site_id',
		],
	];

	protected $has_one = [
		'display_settings' => [
			'model' => 'Location_DisplaySettings',
			'key' => 'location_id'
		]
	];

	protected $has_many = [
		'packs' => [
			'model' => 'Inventory_Pack',
			'key' => 'location_id',
			'cascade_delete' => true
		],
		'locations' => [
			'model' => 'Cases_Item',
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
			$model = $this->pixie->orm->get('Location')->where('name', $this->name)->where('site_id', $this->site_id)->find();
			return !($model->loaded() && $this->id !== $model->id);
		})->error(sprintf('Location with name %s already exists', $this->name));
		return $validator;
	}

	public function getCaseColor()
	{
		return $this->case_color ? $this->case_color : self::DEFAULT_CASE_COLOR;
	}

	public function updateOverviewPosition($overviewPosition)
	{
		if ($this->display_settings && $this->display_settings->overview_position) {
			$this->pixie->db->query('update')
				->table('room_display_settings')
				->data(['overview_position' => $overviewPosition])
				->where('location_id', $this->id)
				->execute();
		} else {
			$this->pixie->db->query('insert')
				->table('room_display_settings')
				->data(['location_id' => $this->id, 'overview_position' => $overviewPosition])
				->execute();
		}
	}

}
