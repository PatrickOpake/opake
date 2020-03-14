<?php

namespace Opake\Exception;

class Ajax extends AbstractException
{

	public function getJsonMessage()
	{
		return json_encode([
			'errors' => $this->getMessage()
		]);
	}

}
