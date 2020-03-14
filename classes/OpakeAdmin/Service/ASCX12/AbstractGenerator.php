<?php

namespace OpakeAdmin\Service\ASCX12;

use OpakeAdmin\Service\ASCX12\General\Request\ISAHeader;

abstract class AbstractGenerator
{
	/**
	 * @var Config
	 */
	protected $config;


	public function __construct()
	{
		$this->config = new Config();
	}

	/**
	 * @param ISAHeader $interchange
	 * @return string
	 */
	protected function generateStructureContent(ISAHeader $interchange)
	{
		$config = $this->config;
		$data = $interchange->generate();
		$lines = [];
		foreach ($data as $segments) {
			$lines[] = implode($config->elementSeparator, $segments);
		}

		return implode($config->segmentSeparator, $lines) . $config->segmentSeparator;
	}

	protected function formatClaimId($claimId)
	{
		return str_pad($claimId, 4, '0', STR_PAD_LEFT);
	}
}