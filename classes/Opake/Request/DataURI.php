<?php

namespace Opake\Request;

class DataURI
{
	/**
	 * @var string
	 */
	protected $mimeType;

	/**
	 * @var array
	 */
	protected $params;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @param $mimeType
	 * @param $params
	 * @param $content
	 */
	public function __construct($mimeType, $params, $content)
	{
		$this->mimeType = $mimeType;
		$this->params = $params;
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getMimeType()
	{
		return $this->mimeType;
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param $url
	 * @return static
	 * @throws \Exception
	 */
	public static function decode($url)
	{
		$regexp = '/data:([a-zA-Z-\/+]+)([a-zA-Z0-9-_;=.+]+)?,(.*)/';
		if (!preg_match($regexp, $url, $matches)) {
			throw new \Exception('Can\'t parse data url');
		}

		$base64 = false;
		$mimeType = $matches[1];
		$params = $matches[2];
		$rawData = $matches[3];
		$dataParams = [];
		if ("" !== $params) {
			foreach (explode(';', $params) as $param) {
				if (strstr($param, '=')) {
					$param = explode('=', $param);
					$dataParams[array_shift($param)] = array_pop($param);
				} elseif ($param === 'base64') {
					$base64 = true;
				}
			}
		}
		if (($base64 && !$rawData = base64_decode($rawData, false))) {
			throw new \Exception('base64 decoding failed');
		}
		if (!$base64) {
			$rawData = rawurldecode($rawData);
		}

		$dataURI = new static($mimeType, $dataParams, $rawData);
		return $dataURI;
	}
}