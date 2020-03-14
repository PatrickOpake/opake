<?php

namespace Opake\ActivityLogger\Extractor\CaseBilling;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class CaseBillingExtractor extends ModelExtractor
{
	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'apcs' => ModelRelationsContainer::HAS_MANY,
			'drgs' => ModelRelationsContainer::HAS_MANY,
			'final_diagnosis' => ModelRelationsContainer::HAS_MANY,
			'admit_diagnosis' => ModelRelationsContainer::HAS_MANY,
			'procedures' => ModelRelationsContainer::HAS_MANY,
			'occurences' => ModelRelationsContainer::HAS_MANY,
			'supplies' => ModelRelationsContainer::HAS_MANY,
			'notes' => ModelRelationsContainer::HAS_MANY,

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

		$result['apcs'] = $relationsContainer->getRelationArrayOfIds('apcs');
		$result['drgs'] = $relationsContainer->getRelationArrayOfIds('drgs');
		$result['final_diagnosis'] = $relationsContainer->getRelationArrayOfIds('final_diagnosis');
		$result['admit_diagnosis'] = $relationsContainer->getRelationArrayOfIds('admit_diagnosis');

		$result['procedures'] = $relationsContainer->getRelation('procedures');
		$result['occurences'] = $relationsContainer->getRelation('occurences');
		$result['supplies'] = $relationsContainer->getRelation('supplies');
		$result['notes'] = $relationsContainer->getRelation('notes');

		return $result;
	}
}
