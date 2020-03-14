<?php

namespace Opake\ActivityLogger\Extractor\ChartGroup;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class ChartGroupExtractor extends ModelExtractor
{
	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'documents' => ModelRelationsContainer::HAS_MANY,
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
		$result['charts'] = $relationsContainer->getRelationArrayOfIds('documents');

		return $result;
	}
}