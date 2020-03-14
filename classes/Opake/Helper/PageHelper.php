<?php

namespace Opake\Helper;

class PageHelper
{

	public static function getErrors($errors)
	{
		if (!sizeof($errors)) {
			return '';
		}
		if (is_string($errors)) {
			$str = $errors;
		} else if (is_array($errors)) {
			$str = '<ul class="list-unstyled"><li>';
			$str .= implode('</li><li>', $errors);
			$str .= '</li></ul>';
		} else {
			return '';
		}
		$str = '<div class="alert alert-danger">' . $str . '</div>';
		return $str;
	}

	public static function getMessage($message)
	{
		if (!$message) {
			return '';
		}
		return '<div class="alert alert-success alert-top-flash">' . $message . '</div>';
	}

}
