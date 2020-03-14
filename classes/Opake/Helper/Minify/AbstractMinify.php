<?php

/**
 * Minify
 *
 */
namespace Opake\Helper\Minify;

/**
 * Обертка, обеспечивающая удобную работу с утилитой minify
 * Позволяет создавать кеш-файлы на лету и затем подключать их без перегенерации кешей.
 * В случае изменения исходников новый кеш будет создан автоматически.
 *
 * Использует две сторонние библиотеку:
 * JSMin+: http://crisp.tweakblogs.net/blog/1665/a-new-javascript-minifier-jsmin+.html
 *
 * Позволяет использовать и другие сторонние библиотеки для сжатия, для этого достаточно переопределить методы compress() и includeLib() в соответствующих классах.
 *
 * @example
 * <code>
 * // задаем конфиг
 *
 * // minify вкл/выкл
 * $config['minify']['enable'] = true;
 * // путь к библиотеке JSMin
 * $config['minify']['jsmin'] = dirname(__FILE__).'/vendors/JSMin.php';
 * // путь к папке с исходниками
 * $config['minify']['base_path'] = dirname(__FILE__).'/admin';
 * // путь к директории с кешем
 * $config['minify']['cache']['path'] = dirname(__FILE__).'/tmp/minify';
 * // путь к директории с кешем относительно веба
 * $config['minify']['cache']['web'] = '/tmp/minify';
 * // время жизни кеша, секунд
 * $config['minify']['ttl'] = 1*24*60*60;
 *
 *
 * // сжимаем css файлы
 * $fileList[] = 'index.css';
 * $fileList[] = 'style.css';
 *
 * $cssHtml = CSSMinify::minify($fileList, $config['minify']);
 *
 * // сжимаем js файлы
 * $fileList = 'index.js';
 *
 * $jsHtml = JSMinify::minify($fileList, $config['minify']);
 * </code>
 */
abstract class AbstractMinify
{

	// конфигурационные настройки
	protected $config = [];
	// подпака для хранения кеша файлов относительно заданной в конфиге
	protected $subfolder = null;
	// исходные коды
	protected $sources = [];

	/**
	 * Конструктор, устанавливает конфигурационные настройки
	 * @param array $config
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * Проверяет наличие кеш-файла и необходимость его обновления
	 * @param string $file - полный путь к файлу кеша
	 * @return bool - true/false
	 */
	protected function checkCache($file)
	{
		if (!file_exists($file)) {
			return false;
		}

		if (filemtime($file) < (time() - $this->config['ttl'])) {
			return false;
		}

		return true;
	}

	/**
	 * Собирает полное имя для файла кеша
	 * @param array $fileList - список исходных файлов
	 * @param string - полный путь к файлу кеша
	 */
	protected function getCacheName($fileList)
	{
		// сортируем список по возрастанию
		sort($fileList);

		// проверяем время обновления файлов
		$lastUpdate = $this->getLastUpdate($fileList);

		// формат файла
		$extension = pathinfo($fileList[0], PATHINFO_EXTENSION);

		$path = implode(',', $fileList);

		$path = md5($path . $lastUpdate);

		if (!empty($extension)) {
			$path .= '.' . $extension;
		}

		$pathFolder = $this->config['cache']['path'] . '/';

		if (!empty($this->subfolder)) {
			$pathFolder .= $this->subfolder . '/';
		}

		$path = $pathFolder . $path;

		return $path;
	}

	/**
	 * Получает метку, характеризующую время последнего обновления для указанного списка файлов.
	 * Используется для автоматического обновления кеша при изменении в файлах.
	 * @param array $fileList - список файлов
	 * @return int - метка обновления файлов
	 */
	protected function getLastUpdate($fileList)
	{
		$lastUpdate = 0;

		foreach ($fileList as $file) {
			$file = $this->getFilePath($file);
			if (!file_exists($file)) {
				throw new Exception\FileNotFound('File not found \'' . $file . '\'');
			}
			$mtime = filemtime($file);
			if (!$mtime) {
				$mtime = 0;
			}
			$lastUpdate += $mtime;
		}

		return $lastUpdate;
	}

	/**
	 * Выполняет сжатие указанного/указанных файлов
	 * Возвращает html готовый к выводу на странице
	 * @param array $fileList - список исходных файлов
	 * @return string - html готовый для вставки на страницу
	 */
	public function run(array $fileList)
	{
		// проверяем включен или выключен упаковщик
		if (!$this->config['enable']) {
			return $this->getHtml($fileList);
		}

		// подключаем библиотеку
		if (!$this->includeLib()) {
			throw new Exception\Config('Incorrect list of sources.');
		}

		// устанавливаем поддиректорию для хранения кеша
		$this->setSubfolder($fileList);

		// получаем имя для файла с кешем
		$cacheName = $this->getCacheName($fileList);

		// проверяем наличие актуального кеш-файла
		if (empty($this->config['force_compile']) && $this->checkCache($cacheName)) {
			return $this->getHtml($cacheName);
		}

		// читаем и сжимаем файлы, если не удается, строим хтмл по исходному списку
		$compressString = $this->compressSources($fileList);
		if (!$compressString) {
			return $this->getHtml($fileList);
		}

		// создаем новый файл
		if (!$this->updateCache($compressString, $cacheName)) {
			return $this->getHtml($fileList);
		}

		// возвращаем html
		return $this->getHtml($cacheName);
	}

	/**
	 * Генерирует html для вывода в документа
	 * @param array $fileList - список файлов для вывода на страницу
	 * @return string - html
	 */
	abstract protected function getHtml($fileList);

	/**
	 * Устанавливает поддиректорию для хранения кеша.
	 * Выбор имени осуществляется исходя из расширения файлов.
	 * Если подпапка указана явным образом, то данный метод сразу вернет true
	 * @param array $fileList - список исходных файлов
	 */
	protected function setSubfolder($fileList)
	{
		if ($this->subfolder !== null) {
			return true;
		}

		$extension = pathinfo($fileList[0], PATHINFO_EXTENSION);

		if (!empty($extension)) {
			$this->subfolder = $extension;
		}

		return true;
	}

	/**
	 * По списку читает исходные файлы и собирает все исходники в одну строку
	 * @param array $fileList - список исходных файлов
	 * @return string - объединенная строка
	 */
	protected function compressSources($fileList)
	{
		$compress = [];

		foreach ($fileList as $file) {

			$file = $this->getFilePath($file);

			if (!file_exists($file)) {
				return false;
			}

			$source = file_get_contents($file);

			if (!$source) {
				return false;
			}

			$row = $this->compress($source, $file);

			// если упаковать не получилось
			if (!$row) {
				return false;
			}

			$compress[] = $row;
		}

		$compressString = implode("\n", $compress);

		return $compressString;
	}

	/**
	 * Сжимает переданный исходный код в строку
	 * @param string $source - исходный код
	 * @param string $file - полный путь к файлу, необходим, к примеру, для корректной обработки URL в CSS
	 * @return string - сжатый исходный код
	 */
	abstract protected function compress($source, $file);

	/**
	 * Обеспечивает подключение дополнительных библиотек, если необходимо.
	 * Для этого должен быть переопределен в дочернем классе.
	 */
	protected function includeLib()
	{
		return true;
	}

	/**
	 * Сохраняет кеш файл на диск
	 * @param string $string - строка для сохранения в кеш
	 * @param string $file - пусть к файлу
	 * @return bool - true/false
	 */
	protected function updateCache($string, $file)
	{
		// проверяем наличие папки для кеш файла
		$dir = dirname($file);

		if (!file_exists($dir) && !mkdir($dir, 0777, true)) {
			return false;
		}

		if (!file_put_contents($file, $string)) {
			return false;
		}

		return true;
	}

	/**
	 * Обертка, обеспечивающая создание экземпляра класса и сжатие исходников
	 * @param array $fileList - список исходных файлов
	 * @param array $config - конфигурационные настройки
	 * @return string - html для вставки на страницу
	 */
	public static function minify(array $fileList, $config)
	{

	}

	/**
	 * Собирает путь к файлу относительно веба для использования в html
	 * @param string $path - полный путь к файлу
	 * @return string - путь относительно веб директории
	 */
	protected function getFilePath($path)
	{
		$commonDir = $this->config['common']['web'];
		if (substr($path, 0, strlen($commonDir)) === $commonDir) {
			return $this->config['common']['path'] . substr($path, strlen($commonDir));
		}
		return $this->config['base_path'] . '/' . $path;
	}

	/**
	 * Собирает путь к файлу относительно веба для использования в html
	 * @param string $path - полный путь к файлу
	 * @return string - путь относительно веб директории
	 */
	protected function getWebPath($path)
	{
		return str_replace($this->config['cache']['path'], $this->config['cache']['web'], $path);
	}

}
