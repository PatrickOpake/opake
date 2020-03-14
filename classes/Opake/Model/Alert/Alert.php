<?php

namespace Opake\Model\Alert;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

class Alert extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'alert';
	protected $_row = [
		'id' => null,
		'type' => null,
		'phase' => 0,
		'date' => null,
		'title' => null,
		'subtitle' => null,
		'object_id' => null,
		'case_id' => null,
		'organization_id' => null,
		'object' => null, //serialized object
	];

	protected $belongs_to = [
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		]
	];

	protected $has_many = [
		'users' => [
			'model' => 'user',
			'through' => 'alert_view',
			'key' => 'alert_id',
			'foreign_key' => 'user_id'
		],
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	public $statuses = [
		self::TYPE_LOW_INVENTORY => 'Low Level',
		self::TYPE_PREFERENCE_CARD => 'Preference Card Needed',
		//self::TYPE_MISSING_INFO => 'Missing Info',
		self::TYPE_MISSING_INVENTORY => 'Missing',
		self::TYPE_CASE_READY_TO_PICKED => 'Case Ready to be picked',
		self::TYPE_CASE_REVIEW => 'Review Case',
		self::TYPE_CASE_REPORT => 'Operative Report Needed',
	];

	const TYPE_LOW_INVENTORY = 0;
	const TYPE_EXPIRING = 1;
	const TYPE_PREFERENCE_CARD = 2;
	const TYPE_MISSING_INFO = 3;
	const TYPE_NEW_ITEMS = 3;
	const TYPE_MISSING_INVENTORY = 4;
	const TYPE_CASE_READY_TO_PICKED = 5;
	const TYPE_CASE_REVIEW = 6;
	const TYPE_CASE_REPORT = 7;

	const PHASE_REQUIRES_ACTION = 0;
	const PHASE_ACTION_TAKEN = 1;
	const PHASE_RESOLVED = 2;

	public function setObject($object)
	{
		$this->object = json_encode($object);
	}

	public function getObject()
	{
		$object = json_decode($this->object, true);
		//Костыль для старых алертов, можно убрать когда база обновится
		if (!isset($object['enddate'])) {
			$object['enddate'] = $this->case->time_end;
		}
		return $object;
	}

	protected function deleteInternal()
	{
		parent::deleteInternal();
		$this->pixie->db->query('delete')->table('alert_view')->where('alert_id', $this->id)->execute();
	}

	public function save()
	{
		$this->date = TimeFormat::formatToDBDatetime(new \DateTime());

		parent::save();
	}
}
