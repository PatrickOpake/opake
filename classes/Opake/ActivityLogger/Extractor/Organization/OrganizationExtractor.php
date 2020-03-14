<?php

namespace Opake\ActivityLogger\Extractor\Organization;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class OrganizationExtractor extends ModelExtractor
{
	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'permissions' => ModelRelationsContainer::HAS_MANY,
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

		$result['permissions'] = [];
		if ($permissions = $relationsContainer->getRelation('permissions')) {
			foreach ($permissions as $permission) {
				$result['permissions'][$permission->permission] = $permission->allowed;
			}
		}

		return $result;
	}
}