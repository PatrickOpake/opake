<?php

namespace Opake\Service\User;

use Opake\Helper\TimeFormat;
use Opake\Model\User as ModelUser;
use Opake\Model\Role as ModelRole;

class User extends \Opake\Service\AbstractService
{

	/**
	 * возвращает список сайтов связанных с организацей $org_id и содержащих в своём названии $query
	 * @param int $org_id
	 * @param string $query
	 * @return array
	 */
	public function getActiveUsers($org_id)
	{
		return $this->orm->get('User')
			->where('organization_id', $org_id)
			->where('status', ModelUser::STATUS_ACTIVE)
			->find_all();
	}

	/**
	 * Возвращает отделы, которые есть в переданном массиве сайтов у нужной организации
	 *
	 * @param int $org_id ID органзиции в которой всё происходит
	 * @param array $site_ids Массив ID сайтов
	 * @retrun array
	 */
	public function getDepartmentsBySites($org_id, $site_ids = NULL)
	{
		$site_ids = $site_ids ? "(" . implode(",", $site_ids) . ")" : '(0)';

		$query = $this->db->query('select')
			->table('department')
			->join('department_site', ['department.id', 'department_site.department_id'])
			->join('site', ['department_site.site_id', 'site.id'])
			->fields('department.*')
			->where('active', true)
			->where('site.organization_id', $org_id)
			->where('site.id', 'IN', $this->db->expr($site_ids))
			->order_by('name', 'asc')
			->limit(12)
			->group_by('department.id');

		return $query->execute()->as_array();
	}

	/**
	 * возвращает список сайтов связанных с организацей $org_id и содержащих в своём названии $query
	 * @param int $org_id
	 * @param string $query
	 * @return array
	 */
	public function getSites($org_id, $query)
	{
		return $this->orm->get('site')
			->where('organization_id', $org_id)
			->where('active', true)
			->where('name', 'like', '%' . $query . '%')
			->order_by('name', 'asc')
			->limit(12)
			->find_all();
	}

	/**
	 * Возвращает URL стартовой страницы пользователя
	 *
	 * @param \Opake\Model\User $user
	 * @return string
	 */
	public function getHomePage($user)
	{
		if($user->isDictation()) {
			return '/operative-reports/my/' . $user->organization_id;
		}

		return ($user->isInternal() ? '/clients/' : '/overview/dashboard/' . $user->organization_id);
	}

	public function updateCaseColors($users)
	{
		if (isset($users) && $users) {
			foreach ($users as $user) {
				$this->updateUserColor($user->id, $user->case_color);
			}
		}
	}

	public function updateUserColor($user_id, $color)
	{
		$model = $this->orm->get('User', $user_id);
		$model->case_color = $color;
		$model->save();
	}

	public function setNewPassword($user, $newPassword)
	{
		$user->setPassword($newPassword);
		$user->last_password_change_date = TimeFormat::formatToDBDatetime(new \DateTime());
	}

	public function updateUsedPasswords($user, $newPassword)
	{
		$isReminderEnabled = $this->pixie->config->get('app.password_change_reminder.enabled');

		if ($isReminderEnabled) {
			$lastPasswordsCount = (int) $this->pixie->config->get('app.password_change_reminder.last_passwords_count');
			$salt = $this->pixie->config->get('app.password_change_reminder.salt');
			$hashPassword = md5($newPassword . $salt);

			$this->pixie->db->query('insert')
				->table('user_last_passwords')
				->data([
					'user_id' => $user->id(),
					'password_hash' => $hashPassword
				])
				->execute();

			$rows = $this->pixie->db->query('select')
				->table('user_last_passwords')
				->fields('id')
				->where('user_id', $user->id())
				->order_by('id', 'DESC')
				->limit($lastPasswordsCount)
				->execute()
				->as_array();

			$lastPasswordIds = [];

			foreach ($rows as $row) {
				$lastPasswordIds[] = $row->id;
			}

			if ($lastPasswordIds) {
				$this->pixie->db->query('delete')
					->table('user_last_passwords')
					->where('id', 'NOT IN', $this->pixie->db->expr("(" . implode(',', $lastPasswordIds) . ")"))
					->where('user_id', $user->id())
					->execute();
			}
		}
	}

	public function checkPasswordNotUsed($user, $password)
	{
		$isReminderEnabled = $this->pixie->config->get('app.password_change_reminder.enabled');
		if (!$isReminderEnabled) {
			return true;
		}

		$salt = $this->pixie->config->get('app.password_change_reminder.salt');
		$hashPassword = md5($password . $salt);

		$row = $this->pixie->db->query('select')
			->table('user_last_passwords')
			->where('user_id', $user->id())
			->where('password_hash', $hashPassword)
			->execute()
			->current();

		return !$row;
	}

}
