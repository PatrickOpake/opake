<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270;

class Header {

	public  $header_label;
	protected $element_array;
	protected $array_position;
	protected $end_element_delimiter = "*";
	protected $end_header_delimiter = '~';

	public function __construct()
	{
		$this->array_position = 0;
	}

	protected function addElement($id, $min, $max, $value)
	{
		$this->element_array[$id] = array(
			'min' => $min,
			'max' => $max,
			'value' => $this->prepareString($value, $max),
			'position' => $this->array_position++
		);
	}

	public function __toString()
	{
		if(isset($this->element_array)){
			$this->sortElementArrayByPosition();
			$output = $this->getAllElementsAsString();
			$output = rtrim($output, "*");
			$output .= $this->end_header_delimiter;
			$output = $this->header_label.$this->end_element_delimiter.$output;
			return $output;
		}
		return '';
	}

	protected function sortElementArrayByPosition()
	{
		usort($this->element_array, function($a, $b) {
			return $a['position'] - $b['position'];
		});
	}

	protected function getAllElementsAsString()
	{
		$output = "";
		foreach($this->element_array as $element){
			$output .= $this->getElementString($element);
		}
		return $output;
	}

	protected function getElementString($element)
	{
		$return = $element['value'];
		$return = $return.$this->end_element_delimiter;
		return $return;
	}

	protected function prepareString($string, $maxlength = 200)
	{
		$string = (string) $string;
		$string = preg_replace('/[^A-Za-z0-9\s\-_\.\,]/','', $string);
		$string = str_replace(["\n", "\r"], '', $string);
		$string = strtoupper($string);
		if (strlen($string) > $maxlength) {
			$string = substr($string, 0, $maxlength);
		}
		return $string;
	}
}
