<?php

/**
 * Права доступа
 */

namespace Opake;

use Opake\Permissions\AccessInspector;
use Opake\Permissions\AccessLevel;

class Permissions
{
	protected $pixie;

	/**
	 * @var AccessInspector
	 */
	protected $inspector;

	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	/**
	 * Возвращает уровень доступа для текущего пользователя, сервиса и действия $action
	 *
	 * Уровни доступа задаются в конфиге roles.php
	 * @param string $action Действие над сущностью
	 * @param string $model Сущность (проверка на случай self доступа)
	 * @return boolean|string
	 */
	public function checkAccess($section, $action, $model = null)
	{
		return $this->getInspector()->checkAccess($section, $action, $model);
	}

	/**
	 * @param string $section
	 * @param string $action
	 * @return AccessLevel
	 */
	public function getAccessLevel($section, $action)
	{
		return $this->getInspector()->getAccessLevel($section, $action);
	}

	/**
	 * @return array
	 */
	public function getPermissionConfig()
	{
		return $this->getInspector()->getPermissionConfig();
	}


	/**
	 * @param \Opake\Model\User $user
	 * @return AccessInspector
	 */
	public function getInspectorForUser($user)
	{
		$authUser = $this->pixie->auth->user();
		if ($authUser && $user->id() == $authUser->id()) {
			return $this->getInspector();
		}

		return $this->createNewInspector($user);
	}

	/**
	 * @return AccessInspector
	 */
	protected function getInspector()
	{
		$authUser = $this->pixie->auth->user();
		if ($this->inspector === null || (!$authUser || $this->inspector->getCurrentUser()->id() != $authUser->id())) {
			$this->inspector = $this->createNewInspector($authUser);
		}

		return $this->inspector;
	}

	protected function createNewInspector($user)
	{
		return new AccessInspector($user);
	}
}
