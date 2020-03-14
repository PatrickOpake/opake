<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;

class CancelAttempt extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'case_cancel_attempt';
	protected $_row = [
		'id' => null,
		'case_cancellation_id' => null,
		'date_called' => null,
		'initials' => null
	];
	protected $belongs_to = [
		'case_cancellation' => [
			'model' => 'Cases_Cancellation',
			'key' => 'case_cancellation_id'
		]
	];

	public function toArray()
	{
		return [
			'id' => (int) $this->id,
			'case_cancellation_id' => (int) $this->case_cancellation_id,
			'date_called' => $this->date_called ? date('D M d Y H:i:s O', strtotime($this->date_called)) : null,
			'initials' => $this->initials
		];
	}

}
