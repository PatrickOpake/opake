<?php

namespace Opake\Helper;

use Opake\Helper\Config;

class Export
{

	public static function pdf($html, $file = null, $options = [])
	{
		$descriptorspec = [
			0 => ['pipe', 'r'], // stdin
			1 => ($file ? ['file', $file, 'w'] : ['pipe', 'w']), // stdout
			2 => ['pipe', 'w'], // stderr
		];
		$parameters = '-q';
		if (!empty($options['landscape'])) {
			$parameters .= ' -O landscape';
		}
		if (!empty($options['page_height'])) {
			$parameters .= ' --page-height ' . $options['page_height'];
		}
		if (!empty($options['page_width'])) {
			$parameters .= ' --page-width ' . $options['page_width'];
		}
		if (!empty($options['page_size'])) {
			$parameters .= ' --page-size ' . $options['page_size'];
		}
		if (!empty($options['margins'])) {
			$parameters .= ' ' . $options['margins'];
		}
		$process = proc_open('"' . Config::get('app.export.pdf') . '" ' . $parameters . ' - -', $descriptorspec, $pipes);

		fwrite($pipes[0], $html);
		fclose($pipes[0]);

		$pdf = true;
		if (isset($pipes[1])) {
			$pdf = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
		}

		$errors = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		proc_close($process);

		return [$pdf, $errors];
	}

	public static function createOpReportPdf($pixie, $html, $headerHtml, $footerHtml)
	{
		$descriptorspec = [
			0 => ['pipe', 'r'], // stdin
			1 => ['pipe', 'w'], // stdout
			2 => ['pipe', 'w'], // stderr
		];
		$parameters = '-q';

		$headerTmpPath = $pixie->app_dir . '/_tmp/' . uniqid() . '.html';
		file_put_contents($headerTmpPath, $headerHtml);
		$parameters .= ' --header-html ' . $headerTmpPath;

		$footerTmpPath = $pixie->app_dir . '/_tmp/' . uniqid() . '.html';
		file_put_contents($footerTmpPath, $footerHtml);
		$parameters .= ' --footer-html ' . $footerTmpPath;

		$parameters .= ' --header-spacing 5 --footer-spacing 2';

		$process = proc_open('"' . Config::get('app.export.pdf') . '" ' . $parameters . ' - -', $descriptorspec, $pipes);

		fwrite($pipes[0], $html);
		fclose($pipes[0]);

		$pdf = true;
		if (isset($pipes[1])) {
			$pdf = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
		}

		$errors = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		proc_close($process);

		unlink($headerTmpPath);
		unlink($footerTmpPath);

		return [$pdf, $errors];
	}

}
