<?php

namespace OpakeAdmin\Controller\Analytics;

use OpakeAdmin\Model\Search\Analytics\UserActivity;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function actionUserActivityTypes()
	{
		$this->result = $this->pixie->activityLogger->getActionsWithTitles();
	}

	public function actionUserActivity()
	{

		$items = [];
		$model = $this->orm->get('Analytics_UserActivity_ActivityRecord');


		$search = new UserActivity($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->getFormatter('ActivityList')->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}
}
