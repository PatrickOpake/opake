<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility;


class Segment extends Loop
{
	protected $parseError;
	protected $fieldSize;
	protected $name;
	protected $content;
	protected $delimiter;
	protected $collection;

	public function __construct($c, $fieldSize, $name)
	{
		$this->parseError = false;
		$this->content = $c;
		$this->delimiter = '*';
		$this->fieldSize = $fieldSize;
		$this->name = $name;

		if(!empty($c)) {
			$this->parse();
		}
	}

	protected function parse()
	{
		$this->collection = [];
		$this->collection[0] = $this->name;
		for($i = 1; $i < $this->fieldSize+1; ++$i) {
			$this->collection[$i] = '';
		}
		if(!empty($this->content)) {
			if($this->content{strlen($this->content) - 1} != '~') {
				$this->parseError = true;
			} else {
				$this->content = rtrim($this->content, '~');
				$pCollection = explode('*', trim($this->content));
				$fieldSize =  min(count($this->collection), count($pCollection));
				for ($i = 0; $i < $fieldSize; $i++) {
					$this->collection[$i] = $pCollection[$i];
				}
			}
		}
	}

	public function size()
	{
		return $this->fieldSize;
	}

	public function loadDefinition()
	{
		// TODO: Implement loadDefinition() method.
	}

	public function toArray()
	{

	}

}