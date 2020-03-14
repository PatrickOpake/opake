<?php

namespace Opake\ActivityLogger\Extractor\Intake;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class PatientInfoExtractor extends ModelExtractor
{
	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'admitting_diagnosis' => ModelRelationsContainer::HAS_ONE,
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
		$result['admitting_diagnosis'] = $relationsContainer->getRelationArrayOfIds('admitting_diagnosis');

		return $result;
	}
}
