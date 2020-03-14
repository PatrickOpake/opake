<?php

namespace Opake\Helper;

class Url
{
	public static function prepareVersionTagUrl($url)
	{
		$app = \Opake\Application::get();
		$url .= (strpos($url, '?') ? '&' : '?') . 'v=' . $app->version_tag;

		return $url;
	}
}