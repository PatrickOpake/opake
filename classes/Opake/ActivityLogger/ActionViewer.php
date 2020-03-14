<?php

namespace Opake\ActivityLogger;

class ActionViewer
{
	/**
	 * @var array
	 */
	protected $actionSettings;

	/**
	 * @var \Opake\Model\Analytics\UserActivity\ActivityRecord
	 */
	protected $activityRecord;

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @param \Opake\Model\Analytics\UserActivity\ActivityRecord $activityRecord
	 * @param array $actionSettings
	 */
	public function __construct($pixie, $activityRecord, $actionSettings = [])
	{
		$this->pixie = $pixie;
		$this->activityRecord = $activityRecord;
		$this->actionSettings = $actionSettings;
	}

	/**
	 * @return array
	 */
	public function formatChanges()
	{
		return $this->getChangesFormatterInstance()->getFormattedData();
	}

	/**
	 * @return array
	 */
	public function formatDetails()
	{
		return $this->getDetailsFormatterInstance()->getFormattedData();
	}

	/**
	 * @return \Opake\ActivityLogger\DefaultFormatter
	 */
	protected function getChangesFormatterInstance()
	{
		if (isset($this->actionSettings['formatter']['changes'])) {
			$formatterClass = $this->actionSettings['formatter']['changes'];
			return new $formatterClass($this->pixie, $this->activityRecord->getChangesArray());
		}

		return new \Opake\ActivityLogger\DefaultFormatter($this->pixie, $this->activityRecord->getChangesArray());
	}

	/**
	 * @return \Opake\ActivityLogger\DefaultFormatter
	 */
	protected function getDetailsFormatterInstance()
	{
		if (isset($this->actionSettings['formatter']['details'])) {
			$formatterClass = $this->actionSettings['formatter']['details'];
			return new $formatterClass($this->pixie, $this->activityRecord->getDetailsArray());
		}

		return new \Opake\ActivityLogger\DefaultFormatter($this->pixie, $this->activityRecord->getDetailsArray());
	}
}