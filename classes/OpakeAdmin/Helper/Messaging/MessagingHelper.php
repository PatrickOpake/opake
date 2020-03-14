<?php

namespace OpakeAdmin\Helper\Messaging;

use Opake\Helper\TimeFormat;
use Opake\Model\User;

class MessagingHelper
{
	const HISTORY_LIMIT = 50;
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var \Opake\Model\User
	 */
	protected $user;

	/**
	 * MessagingHelper constructor.
	 * @param \Opake\Application $pixie
	 * @param \Opake\Model\User $user
	 */
	public function __construct($pixie, $user)
	{
		$this->pixie = $pixie;
		$this->user = $user;
	}

	/**
	 * @param $userDialogId
	 * @return array
	 */
	public function getHistory($userDialogId)
	{
		$mainQuery = $this->pixie->orm->get('Messaging')->where([
				[
					['sender_id', $this->user->id()],
					['recipient_id', $userDialogId]
				],
				[
					'or',
					[
						['sender_id', $userDialogId],
						['recipient_id', $this->user->id()]
					],
				]
			])
			->where('active', 1);

		$lastReadId = $this->pixie->db->query('select')
			->table('messaging')
			->fields('id')
			->where('sender_id', $userDialogId)
			->where('recipient_id', $this->user->id())
			->where('is_read', 1)
			->order_by('id', 'desc')
			->limit(1)
			->execute()
			->get('id');

		if ($lastReadId) {
			$countQuery = clone $mainQuery->query;
			$count = $countQuery->fields($this->pixie->db->expr('COUNT(*) as count'))
				->where('id', '>', $lastReadId)
				->execute()
				->get('count');

			if ($count > self::HISTORY_LIMIT) {
				$query = $mainQuery->where('id', '>', $lastReadId);
			} else {
				$query = $mainQuery->limit(self::HISTORY_LIMIT);
			}
		} else {
			$query = $mainQuery;
		}

		return $query->order_by('id', 'desc')->find_all()->as_array();
	}

	/**
	 * @return \Opake\Model\User
	 */
	public function getUser($id, $witnUnreadCount)
	{
		$model = $this->userQuery($witnUnreadCount);
		$model->where('id', $id);

		return $model->limit(1)->find();
	}

	/**
	 * @return array
	 */
	public function getUsers($witnUnreadCount)
	{
		$model = $this->userQuery($witnUnreadCount);

		if ($witnUnreadCount) {
			$model->query
				->order_by('m.count', 'desc')
				->order_by('user.last_name', 'asc');
		}

		return $model->find_all()->as_array();
	}

	/**
	 * @param $timestamp
	 * @return array
	 */
	public function getLastMessages($timestamp)
	{
		$dateTime = (new \DateTime())->setTimestamp($timestamp);

		return $this->pixie->orm->get('Messaging')
				->where('recipient_id', $this->user->id)
				->where('update_date', '>=', TimeFormat::formatToDBDatetime($dateTime))
				->find_all()
				->as_array();
	}

	/**
	 * @param \OpakeAdmin\Model\Messaging $message
	 */
	public function setMessagesRead($message)
	{
		$this->pixie->db->query('update')->table('messaging')
			->data(['is_read' => 1])
			->where([
				['id', '<=', $message->id()],
				['sender_id', $message->sender_id],
				['recipient_id', $message->recipient_id],
				['is_read', 0]
			])
			->execute();
	}

	/**
	 * @param bool $witnUnreadCount
	 * @return User
	 */
	protected function userQuery ($witnUnreadCount) {
		$model = $this->pixie->orm->get('User')
			->where('organization_id', $this->user->organization_id)
			->where('status', User::STATUS_ACTIVE)
			->where('id', '!=', $this->user->id);

		$model->query->fields($this->pixie->db->expr('`' . $model->table . '`.*'));
		if($witnUnreadCount) {
			$subQuery = $this->pixie->db->query('select')
				->fields('sender_id', $this->pixie->db->expr('COUNT(*) as count'))
				->table('messaging')
				->where([
					['messaging.recipient_id', $this->user->id()],
					['messaging.is_read', 0],
					['messaging.active', 1]
			]);

			$model->query
				->fields($this->pixie->db->expr( 'm.count as unread_count'), $this->pixie->db->expr('`' . $model->table . '`.*'))
				->join([$subQuery, 'm'], ['m.sender_id', 'user.id']);
		}

		if ($this->user->isSatelliteOffice()) {
			$userPracticeGroupIds = $this->user->getPracticeGroupIds();
			if ($userPracticeGroupIds) {
				$model->query
					->join(['user_practice_groups', 'upg'], ['id', 'upg.user_id'], 'left');

				$model->where('upg.practice_group_id', 'IN', $this->pixie->db->arr($userPracticeGroupIds));
			}
		}

		if($this->user->isDoctor()) {
			$userSites = [];
			foreach ($this->user->getSites() as $site) {
				$userSites[] = $site->id();
			}
			if($userSites) {
				$model->query
					->join(['user_site', 'us'], ['id', 'us.user_id'], 'inner');
				$model->where('us.site_id', 'IN', $this->pixie->db->arr($userSites));
			}
		}

		$model->query->group_by('user.id');

		return $model;
	}

}
