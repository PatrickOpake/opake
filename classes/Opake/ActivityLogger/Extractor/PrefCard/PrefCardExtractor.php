<?php

namespace Opake\ActivityLogger\Extractor\PrefCard;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class PrefCardExtractor extends ModelExtractor
{
	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'items' => ModelRelationsContainer::HAS_MANY,
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

		$result['items'] = [];
		if ($items = $relationsContainer->getRelation('items')) {
			$result['items'] = $items;
		}

		$result['notes'] = [];
		if ($notes = $relationsContainer->getRelation('notes')) {
			$result['notes'] = $notes;
		}

		return $result;
	}
}