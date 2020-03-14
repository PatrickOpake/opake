<?php

namespace Opake;

class Response extends \PHPixie\Response
{

	/**
	 * Sends headers to the client
	 *
	 * @return \Opake\Response Resturns itself
	 */
	public function send_headers() {
		foreach ($this->headers as $header)
			header($header, false);
			
		foreach($this->pixie->cookie->get_updates() as $key => $params)
			setcookie($key,
					$params['value'],
					$params['expires'],
					$params['path'],
					$params['domain'],
					$params['secure'],
					$params['http_only']
			);
		
		return $this;
	}

	public function file($mime, $name, $content, $download = true, $inline = false)
	{
		$this->headers = [
			'Content-Type: ' . $mime,
			'Cache-Control: cache, must-revalidate',
			'Pragma: public',
			'Content-Length: ' . strlen($content),
			'Expires: Mon, 26 Jul 1997 05:00:00 GMT',
			'Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT',
		];

		if ($download) {
			$this->headers[] = 'Content-Disposition: attachment; filename="' . basename($name) . '"';
		} else if ($inline) {
			$this->headers[] = 'Content-Disposition: inline; filename="' . basename($name) . '"';
		}

		$this->body = $content;
	}

	public function disableCache()
	{
		$this->headers = [
			'Cache-Control: no-store, no-cache, must-revalidate, max-age=0',
			'Cache-Control: post-check=0, pre-check=0',
			'Pragma: no-cache'
		];
	}

}
