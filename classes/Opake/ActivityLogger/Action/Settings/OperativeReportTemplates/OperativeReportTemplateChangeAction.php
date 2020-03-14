<?php

namespace Opake\ActivityLogger\Action\Settings\OperativeReportTemplates;

use Opake\ActivityLogger\Action\ModelAction;


class OperativeReportTemplateChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'template' => $model->id()
		];
	}


	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [
			'name',
			'cpt_id',
			'anesthesia_administered',
			'ebl',
			'drains',
			'consent',
			'complications',
			'approach',
			'description_procedure',
			'follow_up_care',
			'conditions_for_discharge',
			'scribe',
		];
	}
}