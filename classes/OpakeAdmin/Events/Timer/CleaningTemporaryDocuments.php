<?php

namespace OpakeAdmin\Events\Timer;

use Opake\Events\AbstractListener;
use Opake\Helper\TimeFormat;

class CleaningTemporaryDocuments extends AbstractListener
{

	public function dispatch($event)
	{
		$removeStartTime = new \DateTime();
		$removeStartTime->modify('-1 hour');

		$modelQuery = $this->pixie->orm->get('Document_PrintResult_CleaningQueueRecord');
		$modelQuery->where('is_removed', 0);
		$modelQuery->where('added_date', '<', TimeFormat::formatToDBDatetime($removeStartTime));

		foreach ($modelQuery->find_all() as $model) {
			try {
				$model->removeFiles();
			} catch (\Exception $e) {
				$this->pixie->logger->info('Error while removing record [' . $model->id() . ']');
				$this->pixie->logger->exception($e);
			}

		}
	}

}
