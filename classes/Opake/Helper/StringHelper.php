<?php

namespace Opake\Helper;

class StringHelper
{
	public static function truncate($text, $len = 250, $replace = '...')
	{
		if (mb_strlen($text) <= $len) {
			return $text;
		}

		return mb_substr($text, 0, $len) . $replace;
	}

	public static function strlen($text)
	{
		return mb_strlen((string) $text);
	}

	public static function stripHtmlTags($text, $allowable_tags="")
	{
		return str_replace('&nbsp;', ' ', strip_tags($text, $allowable_tags));
	}

	public static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	public static function removeBrTagFromEnd($text)
	{
		return preg_replace('/((<br \/>))+$/', '', $text);
	}

}