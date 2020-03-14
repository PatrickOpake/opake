<?php

namespace Opake\Model\Geo;

class ZipCode extends \Opake\Model\AbstractModel
{

	public $id_field = 'id';
	public $table = 'geo_zip_code';
	protected $_row = [
		'id' => null,
		'city_id' => null,
		'code' => ''
	];

	public function getList($city)
	{
		if ($city) {
			$this->where('city_id', $city);
		}
		return $this->order_by('code')->find_all();
	}

}
