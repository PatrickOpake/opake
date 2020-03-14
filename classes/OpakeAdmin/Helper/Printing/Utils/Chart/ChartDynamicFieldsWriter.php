<?php

namespace OpakeAdmin\Helper\Printing\Utils\Chart;

class ChartDynamicFieldsWriter
{
	/**
	 * @var \Opake\Model\Forms\Document
	 */
	protected $chart;

	/**
	 * @var array
	 */
	protected $dynamicFields;

	/**
	 * @var string
	 */
	protected $inputFilePath;

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @param \Opake\Model\Forms\Document $chart
	 */
	public function __construct($chart)
	{
		$this->chart = $chart;
	}

	/**
	 * @return bool
	 */
	public function hasDynamicFields()
	{
		return (bool) $this->getDynamicFields();
	}

	/**
	 * @return \Opake\Model\Cases\Item
	 */
	public function getCase()
	{
		return $this->case;
	}

	/**
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function setCase($case)
	{
		$this->case = $case;
	}

	/**
	 * @return \Opake\Model\Forms\Document
	 */
	public function getChart()
	{
		return $this->chart;
	}

	/**
	 * @return string
	 */
	public function getInputFilePath()
	{
		return $this->inputFilePath;
	}

	/**
	 * @param string $inputFilePath
	 */
	public function setInputFilePath($inputFilePath)
	{
		$this->inputFilePath = $inputFilePath;
	}

	/**
	 * @return array
	 */
	public function getDynamicFields()
	{
		if ($this->dynamicFields === null) {
			$this->dynamicFields = $this->chart->dynamic_fields->find_all();
		}

		return $this->dynamicFields;
	}

	public function writeFields()
	{
		if (!$this->inputFilePath) {
			throw new \Exception('Unknown input file');
		}

		$dynamicFields = $this->getDynamicFields();
		if (!$dynamicFields) {
			throw new \Exception('Chart has no dynamic fields');
		}

		$variables = [];
		foreach ($dynamicFields as $model) {
			$variables[$model->page][] = [
				$model->name,
				$model->x,
				$model->y,
				$model->width,
				$model->height
			];
		}

		$writer = new \OpakeAdmin\Helper\Chart\PDF\DynamicFieldsWriter($this->inputFilePath, $variables);
		if ($this->case) {
			$writer->setCase($this->case);
		} else {
			$writer->setPreviewOnly(true);
		}
		$writer->writeFields();
	}
}