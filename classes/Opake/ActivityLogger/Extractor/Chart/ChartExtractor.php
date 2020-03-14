<?php

namespace Opake\ActivityLogger\Extractor\Chart;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class ChartExtractor extends ModelExtractor
{
	/**
	 * @param \Opake\Model\Forms\Document $chart
	 * @return array
	 */
	public function extractChartGroupIds($chart)
	{
		$ids = [];
		foreach ($chart->getChartGroups() as $group) {
			$ids[] = $group->id();
		}

		return $ids;
	}

	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'sites' => ModelRelationsContainer::HAS_MANY,
		    'chart_groups' => [ModelRelationsContainer::CUSTOM_METHOD, [$this, 'extractChartGroupIds']]
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
		$result['chart_groups'] = $relationsContainer->getRelation('chart_groups');

		return $result;
	}
}