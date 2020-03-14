<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util;


use Opake\Helper\StringHelper;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\HL;

class SegmentString
{

	public static function split($string2Split, $segment)
	{
		if (strlen($string2Split) == 0 || !StringHelper::startsWith($string2Split, $segment)) {
			return [];
		}

		$stringQueue = new StringQueue($string2Split);
		$splitList = [];
		$isFirst = true;
		$strBuild = '';

		while ($stringQueue->hasNext()) {
			$peek = $stringQueue->peekNext();
			$next = $stringQueue->getNext();
			if (StringHelper::startsWith($peek, $segment)) {
				if ($isFirst) {
					$isFirst = false;
				} else {
					$splitList[] = $strBuild;
					$strBuild = '';
				}
			}
			$strBuild .= $next;
		}

		if (strlen($strBuild) > 0) {
			$splitList[] = $strBuild;
		}

		return $splitList;
	}

	public static function joinLevel($array)
	{
		$stack = [];
		foreach ($array as $s) {
			$stringQueue = new StringQueue($s);
			if ((new HL($stringQueue->getNext()))->getHierarchicalParentIDNumber() == '') {
				array_push($stack, $s);
			} else {
				$pop = array_pop($stack);
				$pop .= $s;
				array_push($stack, $pop);
			}
		}
		return $stack;

	}

}