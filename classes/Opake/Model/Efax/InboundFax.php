<?php

namespace Opake\Model\Efax;

use Opake\Model\AbstractModel;

class InboundFax extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'efax_inbound_fax';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'site_id' => null,
		'to_fax' => null,
		'from_fax' => null,
		'sent_date' => null,
		'received_date' => null,
		'scrypt_sfax_id' => null,
		'saved_file_id' => null,
	    'is_read' => 0,
	];

	protected $belongs_to = [
		'site' => [
			'model' => 'Site',
		    'key' => 'site_id'
		],
	    'organization' => [
		    'model' => 'Organization',
	        'key' => 'organization_id'
	    ],
	    'saved_file' => [
		    'model' => 'UploadedFile',
	        'key' => 'saved_file_id'
	    ]
	];

	protected $formatters = [
		'WidgetList' => [
			'class' => '\Opake\Formatter\Efax\InboundFax\WidgetListFormatter'
		]
	];

	/**
	 * @param \Opake\Model\User $user
	 */
	public function markAsReadForUser($user)
	{
		$this->is_read = 1;
		$this->save();
	}

	/**
	 * @param \Opake\Model\User $user
	 * @return bool
	 */
	public function isReadByUser($user)
	{
		return ((bool) $this->is_read);
	}

	/**
	 * @param \Opake\Model\User $user
	 */
	public function markAsUnreadForUser($user)
	{
		$this->is_read = 0;
		$this->save();
	}

}