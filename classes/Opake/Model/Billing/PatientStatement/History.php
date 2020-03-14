<?php

namespace Opake\Model\Billing\PatientStatement;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

class History extends AbstractModel
{
	const GENERATED_TYPE_PATIENT_STATEMENT = 1;
	const GENERATED_TYPE_ITEMIZED_BILL = 2;

	public $id_field = 'id';
	public $table = 'patient_statement_history';
	protected $_row = [
		'id' => null,
		'patient_id' => null,
		'date_generated' => null,
		'print_result_id' => null,
	    'is_bulk_print' => null,
		'type' => null,
	];

	protected $belongs_to = [
		'patient' => [
			'model' => 'Patient',
			'key' => 'patient_id'
		],
		'print_result' => [
			'model' => 'Document_PrintResult',
			'key' => 'print_result_id'
		],
	];

	public function save()
	{
		$now = TimeFormat::formatToDBDatetime(new \DateTime());
		$this->date_generated = $now;

		parent::save();
	}

	protected $baseFormatter = [
		'class' => '\Opake\Formatter\Billing\PatientStatement\HistoryFormatter'
	];

	public static function getTypeList()
	{
		return [
			static::GENERATED_TYPE_PATIENT_STATEMENT => 'Patient Statement',
			static::GENERATED_TYPE_ITEMIZED_BILL => 'Itemized Bill'
		];
	}

}
