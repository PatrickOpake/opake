<?php

namespace OpakeApi\Model;

trait Api
{

	public function apiFill(array $rules, $object)
	{
		$data = [];
		foreach ($rules as $key => $value) {
			if (property_exists($object, $key)) {
				$data[$value] = $object->$key;
			}
		}
		return $data;
	}

}
