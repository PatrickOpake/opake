<?php

namespace Opake\Helper;

class SessionHelper
{
	public static function generateHash($length = 42)
	{
		$alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890';
		$pass = [];
		$alphaLength = strlen($alphabet) - 1;
		for ($i = 0; $i < $length; $i++) {
			$n = mt_rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass);
	}
}