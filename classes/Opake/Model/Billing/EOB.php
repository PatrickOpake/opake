<?php

namespace Opake\Model\Billing;

use Opake\Model\AbstractModel;

class EOB extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'billing_eob';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'insurer_id' => null,
		'name' => null,
		'charge_master_id' => null,
		'charge_master_amount' => null,
		'amount_reimbursed' => null,
		'uploaded_file_id' => null,
		'remote_file_id' => null,
		'uploaded_date' => null,
	];
	protected $belongs_to = [
		'file' => [
			'model' => 'UploadedFile',
			'key' => 'uploaded_file_id'
		],
		'charge_master' => [
			'model' => 'Master_Charge',
			'key' => 'charge_master_id'
		],
		'insurer' => [
			'model' => 'Insurance_Payor',
			'key' => 'insurer_id'
		],
		'remote_file' => [
			'model' => 'RemoteStorageDocument',
			'key' => 'remote_file_id'
		]
	];

	protected $baseFormatter = [
		'class' => '\Opake\Formatter\Billing\EOBListFormatter',
	];


}
