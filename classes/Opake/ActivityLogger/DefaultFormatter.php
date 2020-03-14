<?php

namespace Opake\ActivityLogger;

class DefaultFormatter
{

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * @param \Opake\Application $pixie
	 * @param array $data
	 */
	public function __construct($pixie, $data)
	{
		$this->pixie = $pixie;
		$this->data = $data;
	}

	/**
	 * @return array
	 */
	public function getFormattedData()
	{

		if (!$this->data) {
			return [];
		}

		$result = [];

		$formatterLabels = $this->getLabels();
		$ignoredFields = $this->getIgnored();
		$aliases = $this->getAliases();

		$data = $this->prepareDataBeforeFormat($this->data);

		foreach ($data as $fieldName => $fieldValue) {
			$aliasName = (isset($aliases[$fieldName])) ? $aliases[$fieldName] : $fieldName;
			if (in_array($fieldName, $ignoredFields)) {
				continue;
			}
			if ($aliasName !== $fieldName && in_array($aliasName, $ignoredFields)) {
				continue;
			}
			$label = (isset($formatterLabels[$aliasName])) ? $formatterLabels[$aliasName] : $aliasName;
			$formattedValue = $this->formatValue($aliasName, $fieldValue);
			$result[$aliasName] = [$label, $formattedValue];
		}

		$orderedResult = [];
		foreach ($this->getLabels() as $field => $label) {
			if (isset($result[$field])) {
				$label = $result[$field][0];
				$value = $result[$field][1];
				$orderedResult[$label] = $value;
				unset($result[$field]);
			}
		}
		foreach ($result as $field => $value) {
			$label = $result[$field][0];
			$value = $result[$field][1];
			$orderedResult[$label] = $value;
		}

		return $orderedResult;
	}

	protected function prepareDataBeforeFormat($data)
	{
		return $data;
	}

	protected function formatValue($key, $value)
	{
		return $value;
	}

	protected function getAliases()
	{
		return [];
	}

	protected function getIgnored()
	{
		return [];
	}

	protected function getLabels()
	{
		return [];
	}
}