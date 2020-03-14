<?php

namespace Opake\Permissions;

use Opake\Helper\Config;
use Opake\Permissions\AccessLevel;

class AccessInspector
{

	/**
	 * @var \Opake\Model\User
	 */
	protected $user;

	/**
	 * @var array
	 */
	protected $permissionsConfig;

	/**
	 * @param \Opake\Model\User $user
	 */
	public function __construct($user)
	{
		$this->user = $user;
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
		$accessLevel = new AccessLevel($this->getAllowed($section, $action));

		if ($accessLevel->isAllowed()) {
			return true;
		}

		if ($accessLevel->isSelfAllowed()) {
			$user = $this->getCurrentUser();
			if ($user && $model) {
				return $model->isSelf($user);
			}

			// обратная совместимость
			return 'self';
		}


		return false;

	}

	/**
	 * @param string $section
	 * @param string $action
	 * @return AccessLevel
	 */
	public function getAccessLevel($section, $action)
	{
		return new AccessLevel($this->getAllowed($section, $action));
	}

	/**
	 * @return array
	 */
	public function getPermissionConfig()
	{
		$user = $this->getCurrentUser();

		if (!$user) {
			return [];
		}

		if ($this->permissionsConfig === null) {
			$this->permissionsConfig = $this->generatePermissionConfig($user);
		}

		return $this->permissionsConfig;
	}

	/**
	 * @return \Opake\Model\User
	 */
	public function getCurrentUser()
	{
		return $this->user;
	}

	/**
	 * @param string $section
	 * @param string $action
	 * @return bool
	 */
	protected function getAllowed($section, $action)
	{
		$user = $this->getCurrentUser();

		if ($user) {

			if ($user->isInternal()) {
				return true;
			}

			$conf = $this->getPermissionConfig();

			if (!isset($conf[$section][$action])) {
				return false;
			}

			$access = $conf[$section][$action];

			return $access;
		}

		return false;
	}

	/**
	 * @param \Opake\Model\User $user
	 * @return array
	 */
	protected function generatePermissionConfig($user)
	{
		$permissions = [];
		if ($user) {
			$roleConfig = sprintf('permissions.role.%s', $user->role->id());
			if (Config::has($roleConfig)) {
				$configArray = Config::get($roleConfig);
				foreach ($configArray as $sectionName => $actions) {
					foreach ($actions as $actionName => $allowed) {
						if (!isset($permissions[$sectionName][$actionName]) ||
							($permissions[$sectionName][$actionName] === false && $allowed !== false) ||
							($permissions[$sectionName][$actionName] === 'self' && $allowed === true)
						) {
							$permissions[$sectionName][$actionName] = $allowed;
						}
					}
				}
			}
		}

		if ($user->organization) {
			$organizationLevel = new \Opake\Permissions\Organization\OrganizationLevel($user->organization);
			$organizationPermissions = $organizationLevel->getUserPermissions();

			if ($organizationPermissions) {
				foreach ($organizationPermissions as $sectionName => $actions) {
					foreach ($actions as $actionName => $allowed) {
						$permissions[$sectionName][$actionName] = $allowed;
					}
				}
			}
		}

		return $permissions;
	}
}