<?php

namespace OpakeAdmin\Model\Search\Billing\ClaimsProcessing;

use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Search\AbstractSearch;

class Payment extends AbstractSearch
{

	const TABLE_TYPE_PROCESS = 'process';
	const TABLE_TYPE_PROCESSED = 'processed';
	const TABLE_TYPE_RESUBMITTED = 'resubmitted';
	const TABLE_TYPE_ON_HOLD = 'onHold';
	const TABLE_TYPE_EXCEPTION = 'exception';

	/**
	 * @var int
	 */
	protected $bunchId;

	/**
	 * @var int
	 */
	protected $tableType;

	/**
	 * @return int
	 */
	public function getBunchId()
	{
		return $this->bunchId;
	}

	/**
	 * @param int $bunchId
	 */
	public function setBunchId($bunchId)
	{
		$this->bunchId = $bunchId;
	}

	/**
	 * @return int
	 */
	public function getTableType()
	{
		return $this->tableType;
	}

	/**
	 * @param int $tableType
	 */
	public function setTableType($tableType)
	{
		$this->tableType = $tableType;
	}

	public function search($model, $request)
	{
		$db = $this->pixie->db;
		$model = parent::prepare($model, $request);

		$query = $model->query;
		$query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));
		if ($this->getBunchId()) {
			$query->where('payment_bunch_id', $this->getBunchId());
		}
		if ($this->tableType === self::TABLE_TYPE_PROCESS || !$this->tableType) {
			$query->where('status', 'IN', $db->arr([
				\Opake\Model\Billing\Navicure\Payment::STATUS_EXCEPTION,
				\Opake\Model\Billing\Navicure\Payment::STATUS_HOLD,
				\Opake\Model\Billing\Navicure\Payment::STATUS_READY_TO_POST
			]));
		} else if ($this->tableType === self::TABLE_TYPE_PROCESSED) {
			$query->where('status', \Opake\Model\Billing\Navicure\Payment::STATUS_PROCESSED);
		} else if ($this->tableType === self::TABLE_TYPE_RESUBMITTED) {
			$query->where('status', \Opake\Model\Billing\Navicure\Payment::STATUS_RESUBMITTED);
		} else if ($this->tableType === self::TABLE_TYPE_EXCEPTION) {
			$query->where('status', \Opake\Model\Billing\Navicure\Payment::STATUS_EXCEPTION);
		} else if ($this->tableType === self::TABLE_TYPE_ON_HOLD) {
			$query->where('status', \Opake\Model\Billing\Navicure\Payment::STATUS_HOLD);
		}
		$query->order_by('id', 'asc');

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}

		$results = $model->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')
			->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()
			->get('count');

		if ($this->_pagination) {
			$this->_pagination->setCount($count);
		}

		return $results;
	}
}