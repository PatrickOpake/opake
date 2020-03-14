<?php

/**
 * Minify
 *
 */
namespace Opake\Helper\Minify;

class CSSMinify extends AbstractMinify
{

	// экземпляр CSSMin
	protected $cssmin = false;

	public static function minify(array $fileList, $config)
	{
		return (new CSSMinify($config))->run($fileList);
	}

	/**
	 * Сжимает переданный исходный код в строку
	 * @param string $source - исходный код
	 * @param string $file - полный путь к файлу, необходим, к примеру, для корректной обработки URL в CSS
	 * @return string - сжатый исходный код
	 */
	protected function compress($source, $file)
	{
		$res = str_replace(["\r\n", "\r", "\n", "\t"], ' ', trim($source));
		$res = preg_replace("/\/\*.*?\*\//", " ", $res);
		$res = preg_replace("/\s+/", " ", $res);

		$config = $this->config;

		$callback = function ($matches) use ($file, $config) {
			$url = $matches[1];

			// если указан абсолютный путь
			if (preg_match('/^\//', $url) || preg_match('/^https?\:\/\//i', $url) || strpos($url, 'data:') === 0) {
				$url = 'url("' . $url . '")';
				return $url;
			}

			$url = dirname($file) . '/' . $url;
			$url = strtr($url, array('//' => '/'));
			$url = str_replace($config['base_path'], '', $url);
			$url = str_replace($config['common']['path'], $config['common']['web'], $url);
			$url = 'url("' . $url . '")';
			return $url;
		};

		$res = preg_replace_callback("/url\([\'\"]?(.*?)[\'\"]?\)/i", $callback, $res);

		return $res;
	}

	/**
	 * Генерирует html для вывода в документа
	 * @param array $fileList - список файлов для вывода на страницу
	 * @return string - html
	 */
	protected function getHtml($fileList)
	{
		$html = '';

		if (is_string($fileList)) {
			$fileList = [$fileList];
		}

		foreach ($fileList as $file) {
			$file = $this->getWebPath($file);
			$file = \Opake\Helper\Url::prepareVersionTagUrl($file);
			$html .= "<link href='" . $file . "' type='text/css' rel='stylesheet'/>\n";
		}

		return $html;
	}

}
