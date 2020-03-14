<?php

namespace Opake\Model\Cases\Registration\Reconciliation;

use Opake\Model\AbstractModel;
use Opake\Helper\TimeFormat;

class VisitUpdate extends AbstractModel {

	public $id_field = 'id';
	public $table = 'reconciliation_visit_update';
	protected $_row = [
		'id' => null,
		'reconciliation_id' => null,
		'no_change' => 0,
		'change_listed' => 0,
		'date' => null,
	];

	protected $belongs_to = [
		'reconciliation' => [
			'model' => 'Cases_Registration_Reconciliation',
			'key' => 'reconciliation_id',
		]
	];

	public function fromArray($data)
	{
		if (isset($data->date) && $data->date) {
			$data->date = TimeFormat::formatToDB($data->date);
		}

		return $data;
	}

	public function toArray()
	{
		$data = parent::toArray();

		$data['no_change'] = (bool) $this->no_change;
		$data['change_listed'] = (bool) $this->change_listed;
		$data['date'] = $this->date ? date('D M d Y H:i:s O', strtotime($this->date)) : null;

		return $data;
	}

}
