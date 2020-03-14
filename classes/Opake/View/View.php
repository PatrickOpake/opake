<?php

namespace Opake\View;

use Opake\Helper\PageHelper;
use Opake\Helper\Config;
use Opake\Helper\Minify\JSMinify;
use Opake\Helper\Minify\CSSMinify;

class View extends \PHPixie\View
{
	protected $jsFiles = [];
	protected $cssFiles = [];

	protected $minifyEnable = false;
	protected $minifyConfig;

	protected $forceCompileAndMinify = false;

	public $errors = [];

	public function __construct($pixie, $helper, $name)
	{
		parent::__construct($pixie, $helper, $name);

		$config = Config::get('static.minify');
		if ($config && $config['enable']) {
			$this->minifyEnable = true;
			$config['base_path'] = $pixie->app_dir . $config['base_path'];
			$config['common']['path'] = $pixie->root_dir . $config['common']['path'];
			$config['cache']['path'] = $pixie->app_dir . $config['cache']['path'];
			$this->minifyConfig = $config;
		}
	}

	/**
	 * @return boolean
	 */
	public function isForceCompileAndMinify()
	{
		return $this->forceCompileAndMinify;
	}

	/**
	 * @param boolean $forceCompileAndMinify
	 */
	public function setForceCompileAndMinify($forceCompileAndMinify)
	{
		$this->forceCompileAndMinify = $forceCompileAndMinify;
	}

	public function render()
	{
		extract($this->helper->get_aliases());
		extract($this->_data);
		ob_start();
		include($this->path);
		return ob_get_clean();
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function getErrorsHtml()
	{
		return PageHelper::getErrors($this->getErrors());
	}

	public function getMessageHtml()
	{
		return PageHelper::getMessage($this->flash('message'));
	}

	public function setMessage($message)
	{
		$this->pixie->session->flash('message', $message);
	}

	public function addCSSList(array $cssArray)
	{
		$this->cssFiles = array_merge($this->cssFiles, $cssArray);
	}

	public function addJsList(array $jsArray)
	{
		$this->jsFiles = array_merge($this->jsFiles, $jsArray);
	}

	public function addCss($cssFile)
	{
		$this->cssFiles[] = $cssFile;
	}

	public function addJS($jsFile, $inHead = true)
	{
		$this->jsFiles[$jsFile] = $inHead;
	}

	public function addJSFromFolder($path, $inHead = true, $recursive = true)
	{
		$publicPath = $this->pixie->app_dir . 'public';
		
		foreach ($this->getDirectoryFiles($publicPath . $path, 'js', $recursive) as $file) {
			$this->jsFiles[substr($file, strlen($publicPath))] = $inHead;
		}
	}

	public function setDefaultJsCss()
	{
		/* @todo: change to min */

		$this->cssFiles = [
			'/common/vendors/font-awesome/css/font-awesome.min.css',
			'/common/vendors/angular/oi.select-master/dist/select.min.css',
		];

		$this->jsFiles = [
			'/common/vendors/jquery/jquery-2.1.1.min.js' => true,
			'/common/vendors/moment.min.js' => true,
			'/common/vendors/file-upload/jquery.ui.widget.js' => true,
			'/common/vendors/file-upload/jquery.iframe-transport.js' => true,
			'/common/vendors/file-upload/jquery.fileupload.js' => true,
			// Angular Vendors
			'/common/vendors/angular/angular.min.js' => true,
			'/common/vendors/angular/angular-animate.min.js' => true,
			'/common/vendors/angular/angular-sanitize.min.js' => true,
			'/common/vendors/angular/ui-bootstrap-tpls-1.3.3.min.js' => true,
			'/common/vendors/angular/oi.select-master/dist/select-tpls.min.js' => true,
			'/common/vendors/sortable/Sortable.js' => true,
			'/common/vendors/sortable/ng-sortable.js' => true,
		    '/common/vendors/cropit/jquery.cropit.min.js' => true,
		];

		$path = $this->pixie->root_dir . 'apps/common/public/js/core';

		foreach ($this->getDirectoryFiles($path, 'js') as $file) {
			$this->jsFiles['/common/js/core' . substr($file, strlen($path))] = false;
		}
	}

	public function getCssHtml()
	{
		$lines = [];

		if (sizeof($this->cssFiles) && $this->minifyEnable) {
			// готовим список, который нужно минифицировать
			$listMinifyYes = [];
			// готовим список, который не нужно минифицировать
			// в этот список попадут все внешнии ссылки
			$listMinifyNo = [];
			foreach ($this->cssFiles as $line) {
				if (preg_match("/^https?:\/\//", $line)) {
					$listMinifyNo[] = $line;
				} else {
					$listMinifyYes[] = $line;
				}
			}
			try {
				$this->minifyConfig['force'] = $this->forceCompileAndMinify;
				$lines[] = CSSMinify::minify($listMinifyYes, $this->minifyConfig);
			} catch (\Opake\Helper\Minify\Exception\Exception $e) {

				$this->pixie->logger->exception($e);

				// если минифицировать не получилось, считаем, что
				// весь список не нужно минифицировать
				$listMinifyNo = $this->cssFiles;
			}
		} else {
			$listMinifyNo = $this->cssFiles;
		}

		foreach ($listMinifyNo as $cssFile) {
			$lines[] = sprintf(
				"<link href='%s' rel='stylesheet'>",
				($cssFile . (strpos($cssFile, '?') ? '&' : '?') . 'v' . time())
			);
		}

		return implode("\n", $lines) . "\n";
	}

	/**
	 * Выводит список JS файлов
	 * @param bool $inHead Использовать для вывода начальные JS-файлы
	 * @return string
	 */
	public function getJsHtml($inHead = true)
	{
		$result = [];
		foreach ($this->jsFiles as $file => $position) {
			if ($position === $inHead) {
				$result[] = $file;
			}
		}

		$lines = [];
		if (sizeof($result) && $this->minifyEnable) {
			try {
				$this->minifyConfig['force'] = $this->forceCompileAndMinify;
				$lines[] = JSMinify::minify($result, $this->minifyConfig);
				return implode("\n", $lines) . "\n";
			} catch (\Opake\Helper\Minify\Exception\Exception $e) {
				$this->pixie->logger->exception($e);
			}
		}
		foreach ($result as $js) {
			$lines[] = '<script src="' . $js . (strpos($js, '?') ? '&' : '?') . 'v' . time() . '" type="text/javascript"></script>';
		}
		return implode("\n", $lines) . "\n";
	}

	public function flash($messageId)
	{
		try {
			return $this->pixie->session->flash($messageId);
		} catch (\Exception $e) {
			$this->pixie->logger->exception($e);
			return '';
		}
	}

	protected function getDirectoryFiles($path, $ext, $recursive = true)
	{
		$files = [];
		$iterator = new \RecursiveDirectoryIterator ($path, \RecursiveDirectoryIterator::SKIP_DOTS);
		if ($recursive) {
			$iterator = new \RecursiveIteratorIterator ($iterator, \RecursiveIteratorIterator::LEAVES_ONLY);
		}
		$pathes = [];
		foreach ($iterator as $name => $object) {
			if ($object->getExtension() === $ext) {
				$pathes[] = $object->getPath();
				$files[] = $name;
			}
		}
		array_multisort($pathes, $files);
		return $files;
	}

}
