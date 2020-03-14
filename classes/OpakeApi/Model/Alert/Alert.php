<?php

namespace OpakeApi\Model\Alert;

use Opake\Model\Alert\Alert as OpakeAlert;

class Alert extends OpakeAlert
{

	public function toArray()
	{
		return [
			'alertid' => (int)$this->id(),
			'phase' => (int)$this->phase,
			'type' => (int)$this->type,
			'viewed' => (bool)$this->view_date,
			'title' => $this->title,
			'subtitle' => $this->subtitle,
			'date' => $this->date,
			'object_id' => (int)$this->object_id,
			'alertdata' => $this->getObject()
		];
	}

	public function toSmallArray($fields = ['type', 'alertid'])
	{
		if (!is_array($fields)) {
			return $this->toArray();
		} else {
			$result = $this->toArray();
			$result = array_intersect_key($result, array_flip($fields));
			return $result;
		}
	}

}
