<?php

namespace OpakeAdmin\Controller\Chat;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$this->checkAccess('chat', 'view_history');
		$messages = [];

		$chatMessages = $this->orm->get('ChatMessage')->where('organization_id', $this->org->id)->order_by('id', 'desc');

		$search = new \OpakeAdmin\Model\Search\ChatMessages($this->pixie);
		$results = $search->search($chatMessages, $this->request);

		foreach ($results as $result) {
			$messages[] = $result->toArray();
		}

		$totalCount = $search->getPagination()->getCount();

		$this->result = [
			'messages' => $messages,
			'total_count' => $totalCount
		];
	}

	public function actionLastMessages()
	{
		$this->checkAccess('chat', 'messaging');
		$lastRecived = $this->request->param('subid');
		$model = $this->orm->get('ChatMessage')
			->where('organization_id', $this->org->id);

		if ($lastRecived) {
			$model->where('id', '>', $lastRecived);
		}
		$model->order_by('id', 'desc')
			->limit(100);

		$result = [];
		foreach ($model->find_all() as $message) {
			$result[] = $message->toArray();
		}
		$this->result = $result;
	}

	public function actionAddMessage()
	{
		$this->checkAccess('chat', 'messaging');
		$data = $this->getData();

		if ($data) {
			$model = $this->orm->get('ChatMessage');
			$model->organization_id = $this->org->id;
			$model->user_id = $this->logged()->id;

			$this->updateModel($model, $data);
			$this->logged()->updateChatLastReaded($model->id);

			$this->result = $model->toArray();
		}
	}

	public function actionRead()
	{
		$this->checkAccess('chat', 'messaging');
		$lastReaded = $this->request->param('subid');
		if ($lastReaded) {
			$this->logged()->updateChatLastReaded($lastReaded);
		}
	}

}
