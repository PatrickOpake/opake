<?php

namespace OpakeAdmin\Service\ASCX12;

abstract class AbstractResponseSegment extends AbstractSegment
{
	/**
	 * Array of nodes to parse
	 * The method should extract values which are used in application and assign it
	 * to variables
	 *
	 * Example of input data:
	 * [
	 *  ['NM1', 00001, 'SUBMITTER'],
	 *  ['PER', 'New York, Submitter str. 10']
	 * ]
	 *
	 * @param $data
	 */
	public function parseNodes($data)
	{

	}

	/**
	 * Called when method parseNodes() was called for all child nodes
	 */
	public function allNodesParsed()
	{

	}


	protected function explodeComponents($componentsString)
	{
		return explode(':', $componentsString);
	}
}