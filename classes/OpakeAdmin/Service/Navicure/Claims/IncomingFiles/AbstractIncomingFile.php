<?php

namespace OpakeAdmin\Service\Navicure\Claims\IncomingFiles;

use OpakeAdmin\Service\ASCX12\AbstractParser;

abstract class AbstractIncomingFile
{
	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @param string $content
	 */
	public function __construct($content)
	{
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @return null|\OpakeAdmin\Service\ASCX12\AbstractResponseSegment
	 * @throws \Exception
	 */
	public function parse()
	{
		return $this->getParser()->parse($this->content);
	}

	/**
	 * @return AbstractParser
	 */
	abstract public function getParser();

	/**
	 * @return int
	 */
	abstract public function getTransactionId();

	/**
	 * @param \OpakeAdmin\Service\ASCX12\AbstractResponseSegment $rootSegment
	 * @param \Opake\Model\Billing\Navicure\Log $logRecord
	 */
	abstract public function handle($rootSegment, $logRecord);
}