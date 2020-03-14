<?php

namespace Opake;

use Opake\Model\AbstractModel;

class ORM extends \PHPixie\ORM
{

	/**
	 * @var \Opake\Helper\Struct\LimitedMap
	 */
	protected $classesCache;

	/**
	 * Initializes the ORM module
	 *
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 */
	public function __construct($pixie)
	{
		parent::__construct($pixie);

		$this->classesCache = new \Opake\Helper\Struct\LimitedMap(1000);
	}

	/**
	 * Initializes ORM model by name, and optionally fetches an item by id
	 *
	 * @param string $name Model name
	 * @param mixed $id If set ORM will try to load the item with this id from the database
	 * @return \Opake\Model\AbstractModel ORM model, either empty or preloaded
	 * @throws \Exception
	 */
	public function get($name, $id = null)
	{
		$model = $this->tryToResolveModel($name);

		if ($id != null) {
			$model = $model->where($model->id_field, $id)->find();
			$model->values(array($model->id_field => $id));
		}
		return $model;
	}

	/**
	 * @param $name
	 * @return AbstractModel
	 * @throws \Exception
	 */
	protected function tryToResolveModel($name)
	{

		if (isset($this->classesCache[$name])) {
			$className = $this->classesCache[$name];
			return new $className($this->pixie);
		}

		if (strpos($name, "\\") !== false && class_exists($name, true)) {
			$this->classesCache[$name] = $name;
			return new $name($this->pixie);
		}

		$origName = $name;
		$name = explode('_', $name);
		$name = array_map('ucfirst', $name);
		$name = implode("\\", $name);
		foreach ($this->getNamespacesForResolve() as $namespace) {
			$model = $namespace . "Model\\" . $name;
			if (class_exists($model, true)) {
				$this->classesCache[$origName] = $model;
				return new $model($this->pixie);
			}
		}

		throw new \Exception('Unknown ORM model: ' . $name);
	}

	/**
	 * @return array
	 */
	protected function getNamespacesForResolve()
	{
		return [
			$this->pixie->app_namespace,
			Application::COMMON_APP_NAMESPACE
		];
	}
}