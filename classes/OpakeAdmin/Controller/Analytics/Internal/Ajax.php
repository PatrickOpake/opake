<?php

namespace OpakeAdmin\Controller\Analytics\Internal;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}
	}

}
