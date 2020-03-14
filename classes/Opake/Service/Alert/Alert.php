<?php

namespace Opake\Service\Alert;

use Opake\Model\Alert\Alert as OpakeAlert;
use Opake\Helper\TimeFormat;

class Alert extends \Opake\Service\AbstractService
{

	protected $base_model = 'Alert_Alert';

	public function getItems($user_id)
	{
		$alert = $this->orm->get($this->base_model);
		$alert->query->fields('alert.*', 'av.view_date');

		$alert->query->join(
			['alert_view', 'av'], [['alert.id', 'av.alert_id'],
			[
				['or', ['av.user_id', '=', $this->db->expr($user_id)]],
				['or', ['av.user_id', 'IS NULL', $this->db->expr('')]]
			]
		], 'left'
		);
		return $alert;
	}

	/**
	 * Возвращает список алертов для пользователя $user
	 * Если пользователь не указан - будет использован текущий залогиненый
	 *
	 * @param \Opake\User\User $user
	 * @return array
	 */
	public function findAll($user = null, $type = null)
	{
		if (!$user) {
			$user = $this->getUser();
		}
		/* @var $alerts \Opake\Model\Alert\Alert */
		$alerts = $this->getFindSQL($user, $type);
		// для отладки - раскомментируй строку
		// print_r($alerts->query()->query());exit();
		$alerts = $alerts->find_all();
		return $alerts;
	}

	/**
	 * Возвращает один алерт $id для пользователя $user
	 * @param int $id
	 * @param \Opake\Model\User $user
	 * @return \Opake\Model\Alert\Alert
	 */
	public function findOne($id, $user = NULL)
	{
		if (!$user) {
			$user = $this->getUser();
		}
		/* @var $alerts \Opake\Model\Alert\Alert */
		$alert = $this->getFindSQL($user);
		$alert = $alert->where('alert.id', $id)->find();
		return $alert;
	}

	/**
	 * Устанавливает флаг просмотра алерту $alert для пользователя $user
	 * @param \Opake\Model\Alert\Alert $alert
	 * @param \Opake\Model\User $user
	 * @return boolean
	 */
	public function setView($alert, $user = NULL)
	{
		if (!$user) {
			$user = $this->getUser();
		}
		$data = [
			$alert->id(),
			$user->id(),
			strftime(TimeFormat::DATE_FORMAT_DB)
		];
		$this->db->get()->execute('REPLACE `alert_view` SET `alert_id` = ?, `user_id` = ?, `view_date` = ?', $data);
		return true;
	}

	/**
	 * Возвращает ORM модель для поиска алертов для юзера $user
	 *
	 * @param \Opake\Model\User $user
	 */
	protected function getFindSQL($user, $type = null)
	{
		/* @var $alerts \Opake\Model\Alert\Alert */
		$alerts = $this->getItems($user->id);
		// если пользователь не internal - нужно искать алерты только внутри организации
		if (!$user->isInternal()) {

			$alerts->where('organization_id', $user->organization->id);
			if (is_array($type)) {
				$alerts->where('type', 'IN', $this->pixie->db->expr('(' . implode(', ', $type) . ')'));

			} else {
				$alerts->where('type', $type);
			}

			/*if ($user->role->id == Role::Doctor) {
				$alerts->where('type', OpakeAlert::TYPE_PREFERENCE_CARD);
			}*/
		}
		return $alerts;
	}

	/**
	 * Удаляет все отметки о просмотрах для алерта
	 * @param \Opake\Model\Alert\Alert $alert
	 * @return boolean
	 */
	public function deleteView($alert)
	{
		$this->db->query('delete')->table('alert_view')->where('alert_id', $alert->id())->execute();
		return true;
	}

	public function delete($alert)
	{
		$this->deleteView($alert);
		$this->db->query('delete')->table('alert')->where('id', $alert->id())->execute();
		return true;
	}

	public function deleteByCase($case)
	{
		$alerts = $this->orm->get('alert_alert')->where('case_id', $case->id())->find_all()->as_array();
		foreach ($alerts as $alert) {
			$this->db->query('delete')->table('alert_view')->where('alert_id', $alert->id())->execute();
		}

		$this->db->query('delete')->table('alert')->where('case_id', $case->id())->execute();
		return true;
	}

	public function deleteByInventory($type, $ids)
	{
		$alerts = $this->orm->get('Alert_Alert')
			->where('type', $type)
			->where('object_id', 'IN', $this->db->expr("('" . implode("','", $ids) . "')"));

		foreach ($alerts->find_all() as $alert) {
			$alert->delete();
		}
	}
}
