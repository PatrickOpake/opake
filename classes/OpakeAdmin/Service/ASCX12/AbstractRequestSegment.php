<?php

namespace OpakeAdmin\Service\ASCX12;

abstract class AbstractRequestSegment extends AbstractSegment
{
	/**
	 * @return array
	 */
	public function generate()
	{
		$data = [];
		$data = $this->generateSegmentsBeforeChildren($data);
		if ($this->childSegments) {
			foreach ($this->childSegments as $child) {
				$childData = $child->generate();
				$data = array_merge($data, $childData);
			}
		}
		$data = $this->generateSegmentsAfterChildren($data);

		return $data;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	protected function generateSegmentsBeforeChildren($data)
	{
		return $data;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	protected function generateSegmentsAfterChildren($data)
	{
		return $data;
	}

	/**
	 * @param array $components
	 * @return string
	 */
	protected function mergeComponents($components)
	{
		return implode(':', $components);
	}

	/**
	 * @param string $string
	 * @param int $maxlength
	 * @return string
	 */
	protected function prepareString($string, $maxlength = 200)
	{
		$string = (string) $string;
		$string = preg_replace('/[^A-Za-z0-9\s\-_\.\,]/', '', $string);
		$string = preg_replace('/[\-_]/', ' ', $string);
		$string = str_replace(["\n", "\r"], '', $string);
		$string = strtoupper($string);
		if (strlen($string) > $maxlength) {
			$string = substr($string, 0, $maxlength);
		}

		return $string;
	}

	protected function prepareAlphaNumberic($string, $maxlength = 200)
	{
		$string = (string) $string;
		$string = preg_replace('/[^A-Za-z0-9]/', '', $string);
		$string = str_replace(["\n", "\r"], '', $string);
		$string = strtoupper($string);
		if (strlen($string) > $maxlength) {
			$string = substr($string, 0, $maxlength);
		}

		return $string;
	}

	/**
	 * @param string $string
	 * @param int $maxlength
	 * @return string
	 */
	protected function prepareNumber($string, $maxlength = 10)
	{
		$string = (string) $string;
		$string = preg_replace('/[^0-9]/','', $string);
		if (strlen($string) > $maxlength) {
			$string = substr($string, 0, $maxlength);
		}

		return $string;
	}

	/**
	 * @param string $string
	 * @param int $maxLineLength
	 * @return array
	 */
	protected function prepareAddress($string, $maxLineLength = 55)
	{
		$string = $this->prepareString($string);

		if (strlen($string) > $maxLineLength) {
			return [
				substr($string, 0, $maxLineLength),
				substr($string, $maxLineLength, $maxLineLength * 2)
			];
		}

		return [$string];
	}
}