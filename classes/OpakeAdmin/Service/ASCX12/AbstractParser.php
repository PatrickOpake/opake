<?php

namespace OpakeAdmin\Service\ASCX12;

abstract class AbstractParser
{
	/**
	 * @var Config
	 */
	protected $config;

	/**
	 * @var array
	 */
	protected $lines;

	public function __construct()
	{
		$this->config = new Config();
	}

	/**
	 * @param string $content
	 * @return null|AbstractResponseSegment
	 * @throws \Exception
	 */
	public function parse($content)
	{
		if (!$content) {
			throw new \Exception('Empty response');
		}
		$content = trim($content);
		$content = rtrim($content, $this->config->segmentSeparator);
		$lines = explode($this->config->segmentSeparator, $content);
		foreach ($lines as $index => $line) {
			$lines[$index] = explode($this->config->elementSeparator, trim($line));
		}
		$parentElement = null;
		$parsingConfig = $this->getParsingConfig();

		$this->lines = $lines;
		$this->parseForConfig($parsingConfig, $parentElement);

		if ($this->lines) {
			$unprocessedSegments = [];
			foreach ($this->lines as $line) {
				if ($line !== null) {
					$unprocessedSegments[] = ($line[0]) ? $line[0] : '';
				}
			}
			if ($unprocessedSegments) {
				throw new \Exception('Parsing error: Lines [' . implode(', ', $unprocessedSegments) . '] left unprocessed');
			}
		}

		$this->runAllParsedNodes($parentElement);

		return $parentElement;
	}

	protected function parseForConfig($parsingConfig, &$parentElement)
	{
		foreach ($parsingConfig as $confIndex => $conf) {
			$parsingConfig[$confIndex]['originalIndex'] = $confIndex;
		}
		$originalParsingConfig = $parsingConfig;
		foreach ($this->lines as $index => &$line) {
			if (!$line) {
				continue;
			}

			$segmentDefinition = $line[0];
			$continueSearching = false;
			foreach ($parsingConfig as $segmentConfig) {
				if (empty($segmentConfig['segments'][0])) {
					throw new \Exception('At least one segment should be defined');
				}
				$targetSegmentDefinition = $segmentConfig['segments'][0];
				$hasCondition = false;
				$conditionPassed = false;
				if (!empty($segmentConfig['matchMethod'])) {
					call_user_func([$this, $segmentConfig['matchMethod']], $segmentDefinition, $line, $targetSegmentDefinition, $segmentConfig);
				}
				if (($hasCondition && $conditionPassed) || $segmentDefinition === $targetSegmentDefinition) {
					$continueSearching = true;

					$this->segmentMatched($line, $index, $segmentConfig, $parentElement);

					$nextBeginIndex = ($segmentConfig['originalIndex'] + 1);
					$parsingConfig = array_merge(
						array_slice(
							$originalParsingConfig,
							$nextBeginIndex,
							count($segmentConfig)
						),
						array_slice($originalParsingConfig, 0, $nextBeginIndex)
					);

					break;
				}
			}

			if (!$continueSearching) {
				break;
			}
		}
	}

	protected function parseForConfigChildMethod($methodName, &$parentElement)
	{
		foreach ($this->lines as $index => &$line) {
			if (!$line) {
				continue;
			}

			$segmentDefinition = $line[0];
			$segmentConfig = call_user_func([$this, $methodName], $segmentDefinition, $line);

			if (!$segmentConfig) {
				break;
			}

			$this->segmentMatched($line, $index, $segmentConfig, $parentElement);
		}
	}

	protected function segmentMatched($line, $index, $segmentConfig, &$parentElement)
	{
		$segmentNodes = [
			$line
		];
		$this->lines[$index] = null;

		if (!empty($segmentConfig['class'])) {
			$segmentClass = $segmentConfig['class'];
			$segmentObject = new $segmentClass();
			if (!$segmentObject instanceof AbstractResponseSegment) {
				throw new \Exception($segmentClass. ' is not an instance of AbstractResponseSegment');
			}
		} else {
			$segmentObject = new BlankResponseSegment();
		}

		if (count($segmentConfig['segments']) > 1) {
			$additionalSegments = array_slice($segmentConfig['segments'], 1, count($segmentConfig['segments']));
			$segmentNodes = array_merge(
				$segmentNodes,
				$this->takeAdditionalSegments($additionalSegments)
			);
		}

		if (!empty($segmentConfig['children'])) {
			$this->parseForConfig($segmentConfig['children'], $segmentObject);
		} else if (!empty($segmentConfig['childrenMethod'])) {
			$this->parseForConfigChildMethod($segmentConfig['childrenMethod'], $segmentObject);
		}

		if (!empty($segmentConfig['endSegments'])) {
			$additionalSegments = $segmentConfig['endSegments'];
			$segmentNodes = array_merge(
				$segmentNodes,
				$this->takeAdditionalSegments($additionalSegments)
			);
		}

		$segmentObject->parseNodes($segmentNodes);
		if (!$parentElement) {
			$parentElement = $segmentObject;
		} else {
			$parentElement->addChildSegment($segmentObject);
		}
	}

	protected function takeAdditionalSegments($additionalSegments)
	{
		$segmentNodes = [];

		foreach ($this->lines as $index => &$line) {
			if (!$line) {
				continue;
			}
			$nlSegmentDefinition = $line[0];
			$continueSearching = false;
			foreach ($additionalSegments as $targetNlSegmentDefinition) {
				if ($targetNlSegmentDefinition === $nlSegmentDefinition) {
					$segmentNodes[] = $line;
					$continueSearching = true;
					$this->lines[$index] = null;
					break;
				}
			}
			if (!$continueSearching) {
				break;
			}
		}

		return $segmentNodes;
	}

	/**
	 * @param AbstractResponseSegment $segment
	 */
	protected function runAllParsedNodes($segment)
	{
		if ($segment) {
			if ($segment->hasChildSegments()) {
				foreach ($segment->getChildSegments() as $childSegment) {
					$this->runAllParsedNodes($childSegment);
				}
			}

			$segment->allNodesParsed();
		}
	}

	/**
	 * @return array
	 */
	abstract protected function getParsingConfig();
}