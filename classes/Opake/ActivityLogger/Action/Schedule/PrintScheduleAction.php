<?php

namespace Opake\ActivityLogger\Action\Schedule;

use Opake\ActivityLogger\Action\ModelAction;

class PrintScheduleAction extends ModelAction
{
	protected function fetchDetails()
	{
		$details = [];

		if ($viewParams = $this->getExtractor()->getAdditionalInfo('viewParams')) {
			if (isset($viewParams->title)) {
				$details['title'] = $viewParams->title;
			}

			if (isset($viewParams->searchParams)) {
				foreach ($viewParams->searchParams as $paramName => $paramValue) {
					$details[$paramName] = $paramValue;
				}
			}
		}

		return $details;
	}

}