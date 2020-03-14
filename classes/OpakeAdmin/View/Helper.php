<?php

namespace OpakeAdmin\View;

use Opake\Helper\TimeFormat;

class Helper extends \Opake\View\Helper
{

	/**
	 * Constructs the view helper
	 * @param \Opake\Application $pixie Pixie dependency container
	 */
	public function __construct($pixie)
	{
		parent::__construct($pixie);

		$this->aliases = array_merge($this->aliases, [
			'_check_access' => 'check_access',
			'_date' => 'date',
			'_time' => 'time',
			'_ssn' => 'ssn',
			'_money' => 'money',
			'_timeLength' => 'timeLength',
			'_phone' => 'phone',
			'_date_time' => 'date_time',
			'_menu_get_first_has_access' => 'menu_get_first_has_access',
			'_menu_check_access' => 'menu_check_access',
			'_get_access_level' => 'get_access_level',
		    '_prepare_version_tag_url' => 'prepare_version_tag_url'
		]);
	}

	public function check_access($section, $action, $model = null)
	{
		return $this->pixie->permissions->checkAccess($section, $action, $model);
	}

	public function get_access_level($section, $action)
	{
		return $this->pixie->permissions->getAccessLevel($section, $action);
	}

	public function date($date)
	{
		return TimeFormat::getDate($date);
	}

	public function money($float)
	{
		return '$' . number_format((float)$float, 2, '.', ',');
	}

	public function ssn($ssn)
	{
		if(!$ssn) {
			return '';
		}
		return substr($ssn, 0, 3) . '-' . substr($ssn, 3, 2) . '-' . substr($ssn, 5, 4);
	}

	public function timeLength($start, $end)
	{
		$datetime1 = date_create($start);
		$datetime2 = date_create($end);
		$interval = date_diff($datetime1, $datetime2);

		return $interval->format('%h:%I hr');
	}

	public function phone($phone)
	{
		if(!$phone) {
			return '';
		}
		return substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6, 4);
	}

	public function time($date)
	{
		return TimeFormat::getTime($date);
	}

	public function date_time($date)
	{
		return TimeFormat::getDateTime($date);
	}

	public function menu_get_first_has_access($item, $user, $org = null)
	{
		if (!empty($item['url'])) {
			return $item['url'];
		}

		if (!empty($item['items'])) {
			foreach ($item['items'] as $optionItem) {

				if ($this->menu_check_access($optionItem, $user, $org)) {
					if (isset($optionItem['url'])) {
						return $optionItem['url'];
					}

					return $this->menu_get_first_has_access($optionItem, $user, $org);
				}

			}
		}

		return '#';
	}

	public function menu_check_access($item, $user, $org = null)
	{
		if (isset($item['permission'])) {
			$allowed = $this->pixie->permissions->getInspectorForUser($user)->getAccessLevel($item['permission'][0], $item['permission'][1])
				->isAllowed();

			if (!$allowed) {
				return false;
			}

		}

		if($this->check_access_op_reports($item, $user)) {
			return false;
		}

		if ($user->isInternal() && isset($item['internal']) && $item['internal'] == true) {
			return true;
		}

		if (isset($item['internal']) && $item['internal'] == true && !$user->isInternal() ) {
			return false;
		}

		if (isset($item['access']) && !in_array($user->role_id, $item['access'])) {
			return false;
		}

		if (isset($item['callback']) && method_exists($this, $item['callback'])) {
			if (!call_user_func([$this, $item['callback']], $item, $user, $org)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function prepare_version_tag_url($url)
	{
		return \Opake\Helper\Url::prepareVersionTagUrl($url);
	}

	protected function check_access_patient_portal($item, $user, $org)
	{
		if ($org) {
			$orgLevel = new \Opake\Permissions\Organization\OrganizationLevel($org);
			$permissions = $orgLevel->getOrganizationPermissions();
			return (!empty($permissions['patient_portal.login']));
		}

		return false;
	}

	protected function check_access_clinical_item($item, $user, $org)
	{
		if($user) {
			return !$user->isBiller();
		}

		return true;
	}

	protected function check_access_op_reports($item, $user)
	{
		return $item['title'] === 'Operative Reports'
			&& !$user->is_enabled_op_report
			&& !$user->isInternal()
			&& !$user->isFullAdmin()
			&& !$user->isSatelliteOffice();
	}


}
