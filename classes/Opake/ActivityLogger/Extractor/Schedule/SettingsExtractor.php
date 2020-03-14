<?php

namespace Opake\ActivityLogger\Extractor\Schedule;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class SettingsExtractor extends ModelExtractor
{

	/**
	 * @param AbstractModel $model
	 * @param ModelRelationsContainer $relationsContainer
	 */
	protected function extractRelations($model, $relationsContainer)
	{
		parent::extractRelations($model, $relationsContainer);

		if (isset($this->additionalInfo['doctors'])) {
			$colors = [];
			foreach ($this->additionalInfo['doctors'] as $doctor) {
				$user = $model->pixie->orm->get('User', $doctor->id);
				if ($user->loaded()) {
					$colors[$user->id()] = $user->case_color;
				}
			}
			$relationsContainer->addRelation('colors', $colors);
		}

	}

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
		$result['colors'] = $relationsContainer->getRelation('colors');
		return $result;
	}
}