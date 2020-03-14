<?php

namespace Opake\Formatter;

use Opake\Model\AbstractModel;
use Opake\Helper\TimeFormat;

class   BaseDataFormatter extends AbstractFormatter
{

	const ALL_ROW_FIELDS = 'all-row';

	/**
	 * Prepare later in prepareDeferredData
	 *
	 * @var array
	 */
	protected $fieldsForDeferredFormatting = [];

	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return [
			'fields' => self::ALL_ROW_FIELDS
		];
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function toArray()
	{
		$data = $this->prepareBaseData();
		$data = $this->prepareDeferredData($data, $this->fieldsForDeferredFormatting);
		$data = $this->prepareAdditionalData($data);

		return $data;
	}

	protected function init()
	{
		$finalConfig = $this->getDefaultConfig();
		$currentConfig = $this->config;
		$this->config = $this->mergeConfigs($finalConfig, $currentConfig);
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	protected function prepareBaseData()
	{
		$data = [];
		if (!empty($this->config['fields'])) {

			if ($this->config['fields'] === self::ALL_ROW_FIELDS) {
				$modelArray = $this->model->as_array();
				$fieldsList = array_keys($modelArray);
			} else {
				$fieldsList = $this->config['fields'];
			}

			if (isset($this->config['additionalFields'])) {
				$fieldsList = array_merge($fieldsList, $this->config['additionalFields']);
			}

			if (!is_array($fieldsList)) {
				throw new \Exception('Fields list is not array');
			}
			foreach ($fieldsList as $fieldName) {
				$data[$fieldName] = $this->prepareField($fieldName);
			}
		}

		return $data;
	}

	/**
	 * @param $data
	 * @param $fields
	 * @return mixed
	 */
	protected function prepareDeferredData($data, $fields)
	{
		return $data;
	}

	/**
	 * @param $data
	 * @return mixed
	 */
	protected function prepareAdditionalData($data)
	{
		return $data;
	}

	/**
	 * @param $name
	 * @return mixed
	 * @throws \Exception
	 */
	protected function prepareField($name)
	{
		if (isset($this->config['fieldMethods'][$name])) {
			return $this->callFormatMethod($name, $this->config['fieldMethods'][$name], $this->model);
		}

		$modelArray = $this->model->as_array();
		if (array_key_exists($name, $modelArray)) {
			return $modelArray[$name];
		}

		throw new \Exception('Can\'t format field "' . $name . '"');
	}

	/**
	 * @param string $name
	 * @param $methodConfig
	 * @return mixed
	 * @throws \Exception
	 */
	protected function callFormatMethod($name, $methodConfig, $model)
	{
		if (is_array($methodConfig)) {
			$methodName = $methodConfig[0];
			$methodOptions = (isset($methodConfig[1])) ? $methodConfig[1] : [];
		} else {
			$methodName = $methodConfig;
			$methodOptions = [];
		}

		$methodName = 'format' . ucfirst($methodName);
		if (!method_exists($this, $methodName)) {
			throw new \Exception('Unknown method "' . $methodName . '" for field "' . $name . '"');
		}

		return call_user_func([$this, $methodName], $name, $methodOptions, $model, $this->config);
	}

	/**
	 * @param $name
	 * @param $model
	 * @param $config
	 * @return AbstractFormatter
	 * @throws \Exception
	 */
	protected function buildFieldFormatter($name, $model, $config)
	{
		if (!$config) {
			throw new \Exception('Empty formatter config for field "' . $name . '"');
		}

		if (is_array($config)) {
			if (!isset($config['class'])) {
				throw new \Exception('"class" is required field for formatter config for field "' . $name . '"');
			}

			$class = $config['class'];
			return new $class($model, $config);
		} else {
			$class = $config;
			return new $class($model, []);
		}
	}

	/**
	 * @param array $baseConfig
	 * @param array $currentConfig
	 * @return array
	 */
	protected function mergeConfigs($baseConfig, $currentConfig)
	{
		if (isset($currentConfig['fields'])) {
			$baseConfig['fields'] = $currentConfig['fields'];
			unset($currentConfig['fields']);
		}
		if (isset($currentConfig['additionalFields'])) {
			$baseConfig['additionalFields'] = $currentConfig['additionalFields'];
			unset($currentConfig['additionalFields']);
		}

		if (isset($currentConfig['fieldMethods'])) {
			if (!isset($baseConfig['fieldMethods'])) {
				$baseConfig['fieldMethods'] = [];
			}
			$baseConfig['fieldMethods'] = array_replace($baseConfig['fieldMethods'], $currentConfig['fieldMethods']);
			unset($currentConfig['fieldMethods']);
		}

		$baseConfig = array_replace($baseConfig, $currentConfig);

		return $baseConfig;
	}


	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return int
	 */
	protected function formatInt($name, $options, $model)
	{
		$val = $model->{$name};
		return ($val === null) ? null : (int) $val;
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return bool
	 */
	protected function formatBool($name, $options, $model)
	{
		return (bool) $model->{$name};
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return float
	 */
	protected function formatFloat($name, $options, $model)
	{
		$value =  $model->{$name};
		if ($value !== null) {
			$value = (float) $value;

			if (isset($options['round'])) {
				$value = round($value, $options['round']);
			}
		} else {
			if (!empty($options['nullAsZero'])) {
				return 0;
			}
		}

		return $value;
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return string
	 */
	protected function formatMoney($name, $options, $model)
	{
		$value =  $model->{$name};
		if ($value !== null) {
			return '$' . number_format($value, 2, '.', ',');
		}

		return $value;
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return array
	 * @throws \Exception
	 */
	protected function formatRelationshipOne($name, $options, $model)
	{
		$relationshipModel = $model->{$name};
		if (!$relationshipModel->loaded()) {
			return null;
		}
		if (isset($options['formatter'])) {
			$formatter = $this->buildFieldFormatter($name, $relationshipModel, $options['formatter']);
			return $formatter->toArray();
		} else {
			return $relationshipModel->getBaseFormatter()->toArray();
		}
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return array
	 * @throws \Exception
	 */
	protected function formatRelationshipMany($name, $options, $model)
	{
		$relationshipModels = $model->{$name}->find_all();
		$result = [];
		/** @var AbstractModel $relationshipModel */
		foreach ($relationshipModels as $relationshipModel) {
			if (isset($options['formatter'])) {
				$formatter = $this->buildFieldFormatter($name, $relationshipModel, $options['formatter']);
				$result[] = $formatter->toArray();
			} else {
				$result[] = $relationshipModel->getBaseFormatter()->toArray();
			}
		}

		return $result;
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return mixed
	 * @throws \Exception
	 */
	protected function formatAlias($name, $options, $model)
	{
		if (!isset($options['alias'])) {
			throw new \Exception('"alias" is required for field "' . $name . '"');
		}

		$value = $model->{$options['alias']};
		return $value;
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return mixed
	 */
	protected function formatToJsDate($name, $options, $model)
	{
		$value = $model->{$name};
		return TimeFormat::formatToJsDate($value);
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return mixed
	 */
	protected function formatToDateTime($name, $options, $model)
	{
		$value = $model->{$name};
		$date = TimeFormat::fromDBDatetime($value);
		if ($date) {
			return TimeFormat::getDateTime($date);
		}

		return null;
	}

	protected function formatToDate($name, $options, $model)
	{
		$value = $model->{$name};
		$date = TimeFormat::fromDBDate($value);
		if ($date) {
			return TimeFormat::getDate($date);
		}

		return null;
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return mixed
	 * @throws \Exception
	 */
	protected function formatModelMethod($name, $options, $model)
	{
		if (!isset($options['modelMethod'])) {
			throw new \Exception('"modelMethod" is required for field "' . $name . '"');
		}
		$modelMethod = $options['modelMethod'];
		$args = (!empty($options['modelMethodArgs'])) ? $options['modelMethodArgs'] : [];

		return call_user_func_array([$model, $modelMethod], $args);
	}

	/**
	 * @param $name
	 * @param $options
	 * @param $model
	 * @return mixed
	 * @throws \Exception
	 */
	protected function formatDelegateRelationField($name, $options, $model)
	{
		if (!isset($options['relation'])) {
			throw new \Exception('"relation" is required for field "' . $name . '"');
		}

		$relation = $options['relation'];
		$fieldInRelation = $name;
		if (isset($options['fieldInRelation'])) {
			$fieldInRelation = $options['fieldInRelation'];
		}

		$relationModel = $model->{$relation};
		if (!$relationModel->loaded()) {
			if (!empty($options['throwIfNotLoaded'])) {
				throw new \Exception('Relation model "' . $relation . '" is not loaded for field "' . $name . '"');
			} else {
				return null;
			}
		}

		if (isset($options['formatMethod'])) {
			return $this->callFormatMethod($fieldInRelation, $options['formatMethod'], $relationModel);
		} else {
			return $relationModel->{$fieldInRelation};
		}
	}

	protected function formatDeferred($name, $options, $model)
	{
		$this->fieldsForDeferredFormatting[] = $name;
		return null;
	}

}