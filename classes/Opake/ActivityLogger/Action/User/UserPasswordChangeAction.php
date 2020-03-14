<?php

namespace Opake\ActivityLogger\Action\User;

use Opake\ActivityLogger\Comparer\UserChangesComparer;

class UserPasswordChangeAction extends UserChangeAction
{

	protected $forceSave = true;

	/**
	 * @return boolean
	 */
	public function isForceSave()
	{
		return $this->forceSave;
	}

	/**
	 * @param boolean $forceSave
	 * @return $this
	 */
	public function setForceSave($forceSave)
	{
		$this->forceSave = $forceSave;

		return $this;
	}

	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'user' => $model->id()
		];
	}

	/**
	 * @return bool
	 */
	public function isNeedToSave()
	{
		if ($this->isForceSave()) {
			return true;
		}

		if ($this->collectChangesStrategy === static::CHANGES_COMPARE) {
			return (bool)$this->changes;
		}

		return true;
	}


	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [
			'password'
		];
	}

	/**
	 * @return UserChangesComparer
	 */
	protected function createComparer()
	{
		return new UserChangesComparer();
	}

}