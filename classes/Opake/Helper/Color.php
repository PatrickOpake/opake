<?php

namespace Opake\Helper;

class Color
{

	public static function hex2rgba($hex, $opacity)
	{
		list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
		return "rgba($r,  $g,  $b, $opacity)";
	}
}
