<?php

namespace Opake\Model\Organization;

use Opake\Model\AbstractModel;

class Permission extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'organization_permission';

	protected $_row = array(
		'id' => null,
		'organization_id' => null,
		'permission' => null,
		'allowed' => 0
	);

	protected $belongs_to = [
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id'
		],
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

}
