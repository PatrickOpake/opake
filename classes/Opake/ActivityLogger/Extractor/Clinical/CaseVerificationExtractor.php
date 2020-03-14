<?php

namespace Opake\ActivityLogger\Extractor\Clinical;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class CaseVerificationExtractor extends ModelExtractor
{


	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'case_types' => ModelRelationsContainer::HAS_MANY,
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

		$result['case_types'] = $relationsContainer->getRelationArrayOfIds('case_types');

		return $result;
	}
}
