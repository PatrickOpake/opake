<?php

/**
 * Minify
 *
 */
namespace Opake\Helper\Minify;

class JSMinify extends AbstractMinify
{

	public static function minify(array $fileList, $config)
	{
		return (new JSMinify($config))->run($fileList);
	}

	/**
	 * Обеспечивает подключение библиотеки JSMinPlus
	 * @return boolean
	 */
	protected function includeLib()
	{
		if (!isset($this->config['jsmin'])) {
			$this->config['jsmin'] = __DIR__ . '/JSMin.php';
		}
		if (!file_exists($this->config['jsmin'])) {
			throw new Exception\Config('Unable to connect to JSMin class.');
		}

		require_once($this->config['jsmin']);

		if (!class_exists('\JSMin')) {
			throw new Exception\Config('Unable to connect to JSMin class.');
		}
		return true;
	}

	/**
	 * Сжимает переданный исходный код в строку
	 * @param string $source - исходный код
	 * @param string $file - полный путь к файлу, необходим, к примеру, для корректной обработки URL в CSS
	 * @return string - сжатый исходный код
	 */
	protected function compress($source, $file)
	{
		ob_start();
		$res = \JSMin::minify($source);
		ob_end_clean();
		return $res . ';';
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
			$html .= "<script src='" . $file . "' type='text/javascript'></script>\n";
		}

		return $html;
	}

}
