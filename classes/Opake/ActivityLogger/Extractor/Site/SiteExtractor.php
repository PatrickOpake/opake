<?php

namespace Opake\ActivityLogger\Extractor\Site;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class SiteExtractor extends ModelExtractor
{
	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'departments' => ModelRelationsContainer::HAS_MANY,
			'locations' => ModelRelationsContainer::HAS_MANY,

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

		$result['departments'] = $relationsContainer->getRelationArrayOfIds('departments');
		$result['locations'] = $relationsContainer->getRelationArrayOfIds('locations');

		$result = $this->extractGeoData($result);

		return $result;
	}

	protected function extractGeoData($data)
	{
		if (isset($data['country_id'])) {
			$data['country'] = $data['country_id'];
		}

		if (isset($data['pay_country_id'])) {
			$data['pay_country'] = $data['pay_country_id'];
		}

		if (!empty($data['custom_city'])) {
			$data['city'] = $data['custom_city'];
		} else if (!empty($data['city_id'])) {
			$data['city'] = $data['city_id'];
		} else {
			$data['city'] = null;
		}

		if (!empty($data['custom_state'])) {
			$data['state'] = $data['custom_state'];
		} else if (!empty($data['state_id'])) {
			$data['state'] = $data['state_id'];
		} else {
			$data['state'] = null;
		}

		if (!empty($data['pay_custom_city'])) {
			$data['pay_city'] = $data['pay_custom_city'];
		} else if (!empty($data['pay_city_id'])) {
			$data['pay_city'] = $data['pay_city_id'];
		} else {
			$data['pay_city'] = null;
		}

		if (!empty($data['pay_custom_state'])) {
			$data['pay_state'] = $data['pay_custom_state'];
		} else if (!empty($data['pay_state_id'])) {
			$data['pay_state'] = $data['pay_state_id'];
		} else {
			$data['pay_state'] = null;
		}

		return $data;
	}
}
