<?php

namespace Opake\Model;

/**
 * @property \Opake\Model\Organization $organization Organization who placed on site
 */
class Department extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'department';
	protected $_row = array(
		'id' => null,
		'name' => '',
		'active' => true
	);
	protected $has_many = array(
		'sites' => array(
			'model' => 'site',
			'through' => 'department_site',
			'key' => 'department_id',
			'foreign_key' => 'site_id'
		),
	);

	public function toArray()
	{
		return [
			'id' => (int)$this->id(),
			'name' => $this->name,
			'active' => $this->active
		];
	}

}
