<?php

namespace Opake\Extentions\Validate;

use PHPixie\Validate\Field as PHPixieField;

class Field extends PHPixieField
{
	/**
	 * Whether to apply this group only if previous were valid
	 *
	 * @param bool $only_if_valid
	 * @return $this
	 */
	public function only_if_valid($only_if_valid)
	{
		$this->only_if_valid = $only_if_valid;
		return $this;
	}
}