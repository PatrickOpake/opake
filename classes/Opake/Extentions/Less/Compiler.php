<?php

namespace Opake\Extentions\Less;

use Opake\Helper\Config;

if (!class_exists('Less_Parser')) {
	require_once dirname(__FILE__) . '/lib/Autoloader.php';
	\Less_Autoloader::register();
}

class Compiler
{

	/**
	 * Pixie Dependancy Container
	 * @var \Opake\Application
	 */
	protected $pixie;

	// path to store compiled css files
	public $compiled_path = null;

	// if set to true, compileFile method will compile .less to .css ONLY if output .css file not found
	// otherwise compileFile method will only return path and filename of existing .css file
	// this mode is for production
	public $check_files = true;

	public $force_compile = false;

	private $parser = null;

	public function __construct($pixie, $import)
	{
		$this->pixie = $pixie;

		$config = Config::get('static.less');
		$this->check_files = $config['check_files'];
		$this->compiled_path = $pixie->app_dir . $config['compiled_path'];

		$this->parser = new \Less_Parser();
		$this->parser->SetImportDirs($import);
		$this->parser->ModifyVars([
			'version-tag' => 'v-' . $this->pixie->version_tag
		]);
	}

	public function compileFile($file, $fileOut)
	{
		$fileOut = $this->compiled_path . '/' . $fileOut;
		$compile = false;

		if ($this->force_compile) {
			$compile = true;
		} else if ($this->check_files) {
			$files = $this->pixie->cache->get('less-compiler-' . $fileOut);
			if ($files && is_array($files)) {
				foreach ($files as $_file => $_time) {
					if (!file_exists($_file) || filemtime($_file) != $_time) {
						$compile = true;
						break;
					}
				}
			} else {
				$compile = true;
			}
			unset($files);
		}

		if (!file_exists($fileOut) || $compile) {
			$this->parser->parseFile($this->pixie->app_dir . $file);
			file_put_contents($fileOut, $this->parser->getCss());
			$files = [];
			foreach (\Less_Parser::AllParsedFiles() as $f) {
				$files[realpath($f)] = filemtime($f);
			}
			$this->pixie->cache->set('less-compiler-' . $fileOut, $files);
		}

		return $fileOut;
	}

}
