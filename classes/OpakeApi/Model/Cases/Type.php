<?php

namespace OpakeApi\Model\Cases;

use Opake\Model\Cases\Type as OpakeCaseType;

class Type extends OpakeCaseType
{

	public function toArray()
	{
		return [
			'surgerytypeid' => (int)$this->id,
			'surgerytypecode' => $this->code,
			'surgerytypename' => $this->name
		];
	}

}
