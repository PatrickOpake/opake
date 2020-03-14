<?php

namespace Opake\Helper;

class Net
{

	/**
	 * Returns IP address
	 * @param bool $xForwarded If true, use HTTP_X_FORWARDED_FOR
	 * @return string
	 */
	public static function getRemoteAddr($xForwarded = true)
	{
		$remoteIP = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : null;
		$forwardedIP = array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null;
		if ($xForwarded && $forwardedIP) {
			return $forwardedIP;
		} else {
			return $remoteIP;
		}
	}

}
