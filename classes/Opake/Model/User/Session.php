<?php

namespace Opake\Model\User;

class Session extends AbstractSessionModel
{
	const LOGGED_VIA_WEB_INTERFACE = 1;
	const LOGGED_VIA_API = 2;

	public $id_field = 'id';
	public $table = 'user_session';
	protected $_row = [
		'id' => null,
		'user_id' => null,
		'hash' => null,
		'started' => null,
		'expired' => null,
		'is_remember_me' => 0,
		'active' => 1,
		'logged_via' => null
	];

	protected $belongs_to = [
		'user' => [
			'model' => 'User',
			'key' => 'user_id',
		]
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	public function setUserId($userId)
	{
		$this->user_id = $userId;
	}

	public function getUserId()
	{
		return $this->user_id;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function save()
	{
		$this->logged_via = $this->getCurrentLoggedInterface();

		parent::save();
	}

	public function disableAllOtherSessionsForUser($user)
	{
		$model = $this->pixie->orm->get($this->model_name);
		$model->where('active', 1);
		$model->where('user_id', $user->id());
		$model->where('logged_via', $this->getCurrentLoggedInterface());

		foreach ($model->find_all() as $sessionModel) {
			$sessionModel->active = 0;
			$sessionModel->save();
		}
	}

	public function findUserSessionByHash($hash)
	{
		$model = $this->pixie->orm->get($this->model_name);
		$model->where('hash', $hash);
		$model->where('logged_via', $this->getCurrentLoggedInterface());

		$model = $model->find();

		if ($model->loaded()) {
			return $model;
		}

		return null;
	}

	protected function getCurrentLoggedInterface()
	{
		return static::LOGGED_VIA_WEB_INTERFACE;
	}
}