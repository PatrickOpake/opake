<?php

namespace Opake\ActivityLogger\Action\User;

class UserPermissionsChangeAction extends UserChangeAction
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
			'profession_id',
			'role_id'
		];
	}

}