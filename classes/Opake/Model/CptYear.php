<?php

namespace Opake\Model;

class CptYear extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'cpt_year';
	protected $_row = [
		'id' => null,
		'year' => null,
		'note' => ''
	];

	public function toArray()
	{
		return [
			'id' => (int) $this->id,
			'year' => (int) $this->year,
			'note' => $this->note
		];
	}
}
