<?php

namespace Opake\ActivityLogger\Extractor\OperativeReports;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class OperativeReportExtractor extends ModelExtractor
{

	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [

		];
	}

	/**
	 * @param AbstractModel $model
	 * @param ModelRelationsContainer $relationsContainer
	 * @return array
	 */
	protected function modelToArray($model, $relationsContainer)
	{
		$result = parent::modelToArray($model, $relationsContainer);
		return $result;
	}
}