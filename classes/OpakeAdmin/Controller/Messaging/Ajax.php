<?php

namespace OpakeAdmin\Controller\Messaging;

use Opake\Exception\BadRequest;
use OpakeAdmin\Helper\Messaging\MessagingHelper;
use OpakeAdmin\Helper\Messaging\RequestHolder;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	protected $helper;

	public function before()
	{
		parent::before();

		$this->checkAccess('chat', 'messaging');
		$this->iniOrganization($this->request->param('id'));
		$this->helper = new MessagingHelper($this->pixie, $this->logged());
	}

	public function actionPoll()
	{
		$timestamp = (int) $this->request->post('timestamp');
		$iterations = (int) $this->request->post('iterations');

		if (!$timestamp) {
			throw new BadRequest('Bad Request');
		}

		$this->logged()->updateLastActivity();

		$holder = new RequestHolder($this->pixie, $this->logged(), $timestamp, $iterations);
		$holder->run();

		$data = null;
		if ($holder->messages) {
			$messages = [];
			$deleted = [];
			foreach ($holder->messages as $message) {
				if($message->active) {
					$messages[] = $message->toArray();
				} else {
					$deleted[] = [
						'id' => (int) $message->id(),
						'sender_id' => (int) $message->sender_id,
						'is_read' => (bool) $message->is_read
					];
				}
			}
			$data = [
				'messages' => $messages,
				'deleted' => $deleted
			];
		}

		$this->result = [
			'data' => $data,
			'timestamp' => $holder->getTimestamp()
		];
	}

	public function actionUsers()
	{
		$users = [];
		$timestamp = time();

		foreach ($this->helper->getUsers(true) as $user) {
			$photoModel = $user->getPhotoModel();
			$users[] = [
				'id' => (int) $user->id,
				'image' => $photoModel ? $photoModel->getThumbnailWebPath('tiny') : '/i/messaging-default-avatar.png',
				'full_name' => $user->last_name . ', ' . $user->first_name,
				'unread_count' => (int) $user->unread_count,
				'is_online' => $user->isOnline(),
				'is_active' => (bool) $user->is_messaging_active,
			];
		}

		$this->result = [
			'users' => $users,
			'timestamp' => $timestamp
		];
	}

	public function actionUser()
	{
		$userId = $this->request->param('subid');
		if (!$userId) {
			throw new BadRequest('Bad Request');
		}

		$user = $this->helper->getUser($userId, true);
		if (!$user->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$this->result = [
			'id' => (int) $user->id,
			'full_name' => $user->last_name . ', ' . $user->first_name,
			'unread_count' => (int) $user->unread_count,
		];
	}

	public function actionHistory()
	{
		$result = [];
		$userDialogId = $this->request->param('subid');

		foreach ($this->helper->getHistory($userDialogId) as $message) {
			$result[] = $message->toArray();
		}

		$this->result = $result;
	}

	public function actionAdd()
	{
		$data = $this->getData();

		if ($data) {
			$model = $this->orm->get('Messaging');
			$model->sender_id = $this->logged()->id;
			$this->updateModel($model, $data);
			$this->result = $model->toArray();
		}
	}

	public function actionEdit()
	{
		$data = $this->getData();

		if ($data && $data->id) {
			$model = $this->orm->get('Messaging', $data->id);
			$this->updateModel($model, $data);
			$this->result = $model->toArray();
		}
	}

	public function actionRemove()
	{
		$model = $this->loadModel('Messaging', 'subid');
		$model->setInactive();
	}

	public function actionRead()
	{
		$data = $this->getData();
		$messages = [];

		foreach ($data as $messageData) {
			$model = $this->pixie->orm->get('Messaging', $messageData->id);
			if (!$model->loaded() || $model->recipient_id != $this->logged()->id) {
				throw new \Opake\Exception\BadRequest('Wrong message: ' . $messageData->id);
			}
			$messages[] = $model;
		}

		foreach ($messages as $message) {
			$this->helper->setMessagesRead($message);
		}
	}

	public function actionUsersState()
	{
		$users = [];
		foreach ($this->helper->getUsers(false) as $user) {
			$id = (int) $user->id;
			$users[$id] = [
				'is_online' => $user->isOnline(),
				'is_active' => (bool) $user->is_messaging_active,
			];
		}
		$this->result = $users;
	}

	public function actionActivate()
	{
		$this->logged()->updateMessagingActive(1);
	}

	public function actionDeactivate()
	{
		$this->logged()->updateMessagingActive(0);
	}

}
