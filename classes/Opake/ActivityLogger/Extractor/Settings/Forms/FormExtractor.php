<?php

namespace Opake\ActivityLogger\Extractor\Settings\Forms;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class FormExtractor extends ModelExtractor
{

	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'sites' => ModelRelationsContainer::HAS_MANY,
			'case_types' => ModelRelationsContainer::HAS_MANY
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
		$result['case_types'] = $relationsContainer->getRelationArrayOfIds('case_types');

		return $result;
	}
}