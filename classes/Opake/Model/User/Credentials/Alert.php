<?php

namespace Opake\Model\User\Credentials;


use Opake\Model\AbstractModel;

class Alert extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'user_credentials_alert';
	protected $_row = [
		'id' => null,
		'credentials_id' => null,
		'field' => '',
		'status' => self::STATUS_ACTIVE,
	];

	protected $belongs_to = [
		'credentials' => [
			'model' => 'User_Credentials',
			'key' => 'credentials_id'
		]
	];

	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;
}
