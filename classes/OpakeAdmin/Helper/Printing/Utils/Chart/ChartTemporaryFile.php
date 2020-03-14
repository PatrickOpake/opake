<?php

namespace OpakeAdmin\Helper\Printing\Utils\Chart;

class ChartTemporaryFile
{
	/**
	 * @var \Opake\Model\Forms\Document
	 */
	protected $chart;

	/**
	 * @var string
	 */
	protected $filePath;

	/**
	 * @param \Opake\Model\Forms\Document $chart
	 */
	public function __construct($chart)
	{
		$this->chart = $chart;
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getFilePath()
	{
		if (!$this->filePath) {
			throw new \Exception('File has not been created');
		}
		return $this->filePath;
	}

	public function createFile()
	{
		$file = $this->chart->file;
		if (!$file->loaded()) {
			throw new \Exception('File is not loaded');
		}

		$app = \Opake\Application::get();
		$filePath = $file->getSystemPath();
		$tmpPath = $app->app_dir . '_tmp/chart-temp-' . uniqid() . '.pdf';
		$this->filePath = $tmpPath;
		$copyResult = copy($filePath, $tmpPath);
		if ($copyResult === false) {
			throw new \Exception('Can\'t copy original PDF');
		}
	}

	public function readContent()
	{
		if (!file_exists($this->filePath)) {
			throw new \Exception('Temporary file doesn\'t exist');
		}
		return file_get_contents($this->filePath);
	}

	public function cleanup()
	{
		if (file_exists($this->filePath)) {
			unlink($this->filePath);
		}
	}
}