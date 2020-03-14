<?php

namespace Opake\ActivityLogger;

use Opake\Model\AbstractModel;

class ModelRelationsContainer
{
	const HAS_ONE = 'one';
	const HAS_MANY = 'many';
	const CUSTOM_METHOD = 'custom_method';

	/**
	 * @var array
	 */
	protected $relations = [];

	/**
	 * @param AbstractModel $model
	 * @param array $names
	 */
	public function extractRelations($model, $names)
	{
		$model->cached = [];
		foreach ($names as $name => $resultType) {
			if (!array_key_exists($name, $this->relations)) {

				$args = [];
				if (is_array($resultType)) {
					$args = array_slice($resultType, 1, count($resultType));
					$resultType = $resultType[0];
				}

				if ($resultType === static::CUSTOM_METHOD) {
					if (isset($args[0]) && is_callable($args[0])) {
						$callable = $args[0];
						$this->relations[$name] = call_user_func($callable, $model);
						continue;
					}
				}

				if (strpos($name, '.') !== false) {
					$subNames = explode('.', $name);
					$currentNames = [];
					$currentModel = $model;
					foreach ($subNames as $subName) {
						$currentNames[] = $subName;
						$newModelName = implode('.', $currentNames);
						$this->relations[$newModelName] = null;
						$newModel = $currentModel->{$subName};
						//if not last iteration
						if (count($currentNames) !== count($subNames)) {
							if ($newModel === null || !$newModel->loaded()) {
								break;
							}
							$this->relations[$newModelName] = $newModel;
						} else {
							$this->relations[$newModelName] = $this->extractModelByType($currentModel, $newModel, $resultType, $args);
						}

						$currentModel = $newModel;
					}
				} else {
					$this->relations[$name] = null;
					$relation = $model->{$name};
					if ($relation === null) {
						continue;
					}

					$this->relations[$name] = $this->extractModelByType($model, $relation, $resultType, $args);
				}
			}
		}
	}

	public function addRelation($name, $relation)
	{
		$this->relations[$name] = $relation;
	}

	/**
	 * @param $name
	 * @return mixed
	 */
	public function getRelation($name)
	{
		return (isset($this->relations[$name])) ? $this->relations[$name] : null;
	}

	/**
	 * @param $name
	 * @return array
	 */
	public function getRelationArrayOfIds($name)
	{
		if (isset($this->relations[$name])) {
			if ($this->checkIsIterable($this->relations[$name])) {
				$result = [];
				foreach ($this->relations[$name] as $model) {
					$result[] = (int) $model->id();
				}

				return $result;
			}
		}

		return [];
	}

	public function getRelationArrayOfFields($name, $fieldName)
	{
		if (isset($this->relations[$name])) {
			if ($this->checkIsIterable($this->relations[$name])) {
				$result = [];
				foreach ($this->relations[$name] as $model) {
					$result[] = $model->{$fieldName};
				}

				return $result;
			}
		}

		return [];
	}

	protected function extractModelByType($originalModel, $relation, $resultType, $args)
	{
		if ($resultType === static::HAS_ONE) {
			if ($relation->loaded()) {
				return $relation;
			}

			return null;

		} else if ($resultType === static::HAS_MANY) {
			$resultArray = [];
			foreach ($relation->find_all() as $resultModel) {
				if ($resultModel->loaded()) {
					$resultArray[] = $resultModel;
				}
			}

			return $resultArray;
		}

		return null;
	}

	/**
	 * @param $item
	 * @return bool
	 */
	protected function checkIsIterable($item)
	{
		return (is_array($item) || ($item instanceof \stdClass) || ($item instanceof \Traversable));
	}

}