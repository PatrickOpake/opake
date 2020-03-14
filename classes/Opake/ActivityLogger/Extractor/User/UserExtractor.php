<?php

namespace Opake\ActivityLogger\Extractor\User;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class UserExtractor extends ModelExtractor
{

	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'sites' => ModelRelationsContainer::HAS_MANY,
			'departments' => ModelRelationsContainer::HAS_MANY
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
		$result['sites'] = $relationsContainer->getRelationArrayOfIds('sites');
		$result['departments'] = $relationsContainer->getRelationArrayOfIds('departments');

		return $result;
	}
}