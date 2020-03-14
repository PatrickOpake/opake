<?php

namespace Opake\ActivityLogger;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

abstract class AbstractAction
{
	const CHANGES_DONT_STORE = 0;
	const CHANGES_COMPARE = 1;
	const CHANGES_ADD_ALL = 2;

	const FIELDS_ALL = 'all';

	protected $collectChangesStrategy = self::CHANGES_DONT_STORE;
	protected $type;
	protected $details = [];
	protected $changes = [];

	protected $pixie;
	protected $userId;

	/**
	 * @var AbstractExtractor
	 */
	protected $extractor;

	/**
	 * @var bool
	 */
	protected $isDataFetched = false;

	/**
	 * @param \Opake\Application $pixie
	 * @param int $actionType
	 */
	public function __construct($pixie, $actionType)
	{
		$this->pixie = $pixie;
		$this->type = $actionType;
	}

	/**
	 * @param mixed $userId
	 * @return $this
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getCollectChangesStrategy()
	{
		return $this->collectChangesStrategy;
	}

	/**
	 * @param int $collectChangesStrategy
	 * @return $this
	 */
	public function setCollectChangesStrategy($collectChangesStrategy)
	{
		$this->collectChangesStrategy = $collectChangesStrategy;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getActionType()
	{
		return $this->type;
	}


	public function getUserId()
	{
		if ($this->userId) {
			return $this->userId;
		}

		$user = $this->pixie->auth->user();
		if (!$user) {
			throw new \Exception('Unknown user for activity logger');
		}

		return $user->id();
	}

	/**
	 * @return AbstractExtractor
	 */
	public function getExtractor()
	{
		if (!$this->extractor) {
			$this->extractor = $this->createExtractor();
		}

		return $this->extractor;
	}

	/**
	 * @return bool
	 */
	public function isNeedToSave()
	{
		if ($this->collectChangesStrategy === static::CHANGES_COMPARE) {
			return (bool)$this->changes;
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function register()
	{
		$this->details = $this->fetchDetails();
		$this->changes = $this->fetchChanges();

		$record = $this->makeRecord();
		if ($this->isNeedToSave()) {
			$record->save();
			$this->saveSearchParams($record);
			return true;
		}

		return false;
	}

	/**
	 * @return AbstractModel
	 * @throws \Exception
	 */
	protected function makeRecord()
	{
		$model = $this->pixie->orm->get('Analytics_UserActivity_ActivityRecord');
		$model->action = $this->getActionType();
		$model->user_id = $this->getUserId();
		if ($changes = $this->changes) {
			$model->changes = serialize($this->changes);
		}
		if ($details = $this->details) {
			$model->details = serialize($this->details);
		}

		$currentDate = new \DateTime();
		$model->date = TimeFormat::formatToDBDatetime($currentDate);

		return $model;
	}

	protected function saveSearchParams($model)
	{
		$searchParams = $this->getSearchParams();
		if ($searchParams) {
			$this->pixie->db->query('insert')
				->table('user_activity_search_params')
				->data(array_merge(['user_activity_id' => $model->id()], $searchParams))
				->execute();
		}
	}


	/**
	 * @return array
	 */
	protected function fetchDetails()
	{
		return [];
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	protected function fetchChanges()
	{
		$comparer = $this->createComparer();
		$comparer->setExtractor($this->getExtractor());
		$comparer->setCollectChangesStrategy($this->collectChangesStrategy);
		$comparer->setFieldsForCompare($this->getFieldsForCompare());
		$comparer->setIgnoredFields($this->getIgnoredFieldsForCompare());

		return $comparer->fetchChanges();
	}

	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return static::FIELDS_ALL;
	}

	/**
	 * @return array
	 */
	protected function getIgnoredFieldsForCompare()
	{
		return [];
	}

	/**
	 * @return array
	 */
	protected function getSearchParams()
	{

	}

	/**
	 * @return ChangesComparer
	 */
	protected function createComparer()
	{
		return new ChangesComparer();
	}

	/**
	 * @return AbstractExtractor
	 */
	abstract protected function createExtractor();

}