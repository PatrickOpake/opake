<?php

namespace Opake\ActivityLogger\Extractor\Billing;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class BillingPaymentExtractor extends ModelExtractor
{
	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'payment_info' => ModelRelationsContainer::HAS_ONE,

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

		$result['payment_info'] = $relationsContainer->getRelation('payment_info');

		return $result;
	}
}
