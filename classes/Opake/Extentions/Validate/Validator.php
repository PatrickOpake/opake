<?php

namespace Opake\Extentions\Validate;

class Validator extends \PHPixie\Validate\Validator
{

	/**
	 * @return array
	 */
	public function common_errors_list()
	{
		if ($this->errors) {
			$fullList = [];
			foreach ($this->errors as $fieldErrors) {
				$fullList = array_merge($fullList, $fieldErrors);
			}
			return $fullList;
		}

		return [];
	}

	/**
	 * @return string
	 */
	public function first_error_key()
	{
		if ($this->errors) {
			reset($this->errors);
			return key($this->errors);
		}

		return null;
	}

	/**
	 * @return string
	 */
	public function first_error_message()
	{
		if ($this->errors) {
			$firstField = reset($this->errors);
			return reset($firstField);
		}

		return null;
	}
}