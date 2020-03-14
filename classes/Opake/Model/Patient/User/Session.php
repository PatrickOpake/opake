<?php

namespace Opake\Model\Patient\User;

use Opake\Model\User\AbstractSessionModel;

class Session extends AbstractSessionModel
{
	public $id_field = 'id';
	public $table = 'patient_user_session';
	protected $_row = [
		'id' => null,
		'patient_user_id' => null,
		'hash' => null,
		'started' => null,
		'expired' => null,
		'is_remember_me' => 0,
		'active' => 1,
	];

	protected $belongs_to = [
		'patient_user' => [
			'model' => 'Patient_User',
			'key' => 'patient_user_id',
		]
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	public function generateHash()
	{
		$this->hash = md5(uniqid());
	}

	public function setUserId($userId)
	{
		$this->patient_user_id = $userId;
	}

	public function getUserId()
	{
		return $this->patient_user_id;
	}

	public function getUser()
	{
		return $this->patient_user;
	}

	public function disableAllOtherSessionsForUser($user)
	{
		$model = $this->pixie->orm->get('Patient_User_Session');
		$model->where('active', 1);
		$model->where('patient_user_id', $user->id());

		foreach ($model->find_all() as $sessionModel) {
			$sessionModel->active = 0;
			$sessionModel->save();
		}
	}

	public function findUserSessionByHash($hash)
	{
		$model = $this->pixie->orm->get('Patient_User_Session');
		$model->where('hash', $hash);

		$model = $model->find();

		if ($model->loaded()) {
			return $model;
		}

		return null;
	}

}