<?php

namespace Opake\Extentions;

use PHPixie\Validate as PHPixieValidator;
use Opake\Extentions\Validate\Ruleset;
use Opake\Extentions\Validate\Field;

class Validate extends PHPixieValidator
{

	/**
	 * Creates a Validator instance and intializes it with input data
	 *
	 * @param   array  $input  Associative array of fields and values
	 * @return  \Opake\Extentions\Validate\Validator   Initialized Validator object
	 */
	public function get($input = [])
	{
		return new \Opake\Extentions\Validate\Validator($this->pixie, $input);
	}

	/**
	 * Creates a Field instance
	 *
	 * @param   string $name Name of the field to validate
	 * @param   boolean $only_if_valid Marks this Field to only be processed if previous
	 *                                 definitions of this field were valid
	 * @return  \Opake\Extentions\Validate\Field Field instance
	 */
	public function field($name)
	{
		return new Field($this->pixie, $name);
	}

	/**
	 * Builds a Ruleset instance
	 *
	 * @return  \Opake\Extentions\Validate\Ruleset  Ruleset instance
	 */
	public function build_ruleset()
	{
		return new Ruleset();
	}
}

