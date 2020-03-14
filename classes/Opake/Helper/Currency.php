<?php

namespace Opake\Helper;

class Currency
{

	public static function formatUSD($value)
	{
		return '$' . number_format($value, 2);
	}

	public static function parseString($str)
	{
		return str_replace([',', ' ', '$'], ['.'], $str);
	}
}
