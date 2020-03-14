<?php

namespace Opake\ActivityLogger\Action\User;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Comparer\UserChangesComparer;
use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\Extractor\User\UserExtractor;

class UserChangeAction extends ModelAction
{

	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'user' => $model->id()
		];
	}

	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [
			'id',
			'email',
			'first_name',
			'last_name',
			'address',
			'phone',
			'country_id',
			'comment',
			'status',
			'photo_id',
			'sites',
			'departments'
		];
	}

	/**
	 * @return UserChangesComparer
	 */
	protected function createComparer()
	{
		return new UserChangesComparer();
	}

	/**
	 * @return ModelExtractor
	 */
	protected function createExtractor()
	{
		return new UserExtractor();
	}

}