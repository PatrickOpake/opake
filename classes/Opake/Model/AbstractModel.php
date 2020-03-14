<?php

namespace Opake\Model;

use Opake\Formatter\AbstractFormatter;
use Opake\Helper\Pagination;

abstract class AbstractModel extends AbstractPixieModel
{
	protected $_filled_relations = [];
	public $fire_events = true;

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter'
	];

	/**
	 * @var array
	 */
	protected $formatters = [];

	/**
	 * @var AbstractFormatter
	 */
	protected $baseFormatterInstance;

	/**
	 * Определяет принадлежность модели
	 * @param \Opake\Model\User $user
	 * @return boolean
	 */
	public function isSelf($user)
	{
		if (isset($this->user_id)) {
			return $this->user_id === $user->id;
		}
		return false;
	}

	public function generateId()
	{
		return $this->conn->execute('CALL ai_update(\'' . $this->table . '\', @p0);')->get('ai');
	}

	public function fill($data)
	{
		if (method_exists($this, 'fromArray')) {
			if (is_array($data)) {
				$data = json_decode(json_encode($data), false);
			}
			$data = $this->fromArray($data);
		}
		foreach ($data as $field => $value) {
			if (array_key_exists($field, $this->_row)) {
				$this->_row[$field] = $value === '' ? null : $value;
			} elseif (isset($this->has_many[$field])) {
				$rel = $this->has_many[$field];
				if (isset($rel['through'])) {
					$items = [];
					foreach ($value as $item) {
						if (!is_object($item)) {
							$item = $this->pixie->orm->get($rel['model'], $item);
						}
						$items[] = $item;
					}
					$this->_filled_relations[$field] = $items;
				}
			}
		}
	}

	public function save()
	{
		// Актуально для MySQL, а ты сыпит стриктами в случае STRICT_TRANS_TABLES
		foreach ($this->_row as $field => $value) {
			if (is_bool($value)) {
				$this->_row[$field] = (int)$value;
			}
		}

		parent::save();

		if ($this->_filled_relations) {
			foreach ($this->_filled_relations as $field => $values) {
				$rel = $this->has_many[$field];
				if (isset($rel['through'])) {
					//extended overwrite options
					if (!empty($rel['overwrite']['replace'])) {
						$this->overwriteReplaceRelationValues($values, $rel['overwrite'], $rel);
					} else {
						//old overwrite way
						$vals = [];
						foreach ($values as $v) {
							$vals[$v->id] = $v;
						}
						if (!empty($rel['overwrite'])) {
							foreach ($this->$field->find_all() as $item) {
								$this->remove($field, $item);
							}
						} else {
							foreach ($this->$field->find_all() as $item) {
								if (!isset($vals[$item->id])) {
									$this->remove($field, $item);
								} else {
									unset($vals[$item->id]);
								}
							}
						}
						foreach ($vals as $v) {
							$this->add($field, $v);
						}
					}
				}
			}
			$this->_filled_relations = [];
		}
		if ($this->fire_events) {
			$this->pixie->events->fireEvent('save.' . $this->table, $this);
		}
	}

	/**
	 * @return \PHPixie\Validate\Validator
	 */
	public function getValidator()
	{
		return $this->pixie->validate->get(array_merge($this->_row, $this->_filled_relations));
	}

	/**
	 * @param Pagination $pages
	 * @return mixed
	 */
	public function pagination(Pagination $pages)
	{
		return $this->offset($pages->getPage() * $pages->getLimit())->limit($pages->getLimit());
	}

	/**
	 * @return mixed
	 */
	public function toArray()
	{
		return $this->getBaseFormatter()->toArray();
	}

	/**
	 * @return AbstractFormatter
	 * @throws \Exception
	 */
	public function getBaseFormatter()
	{
		if (!$this->baseFormatterInstance) {
			return $this->buildFormatterFromConfig($this->baseFormatter);
		}

		return $this->baseFormatterInstance;
	}

	/**
	 * @param string|null $name
	 * @return AbstractFormatter
	 * @throws \Exception
	 */
	public function getFormatter($name = null)
	{
		if ($name === null) {
			return $this->getBaseFormatter();
		}

		if (isset($this->formatters[$name])) {
			$config = $this->formatters[$name];
			return $this->buildFormatterFromConfig($config);
		}

		throw new \Exception('Unknown formatter with name "' . $name . '" for model ' . get_class($this) . '');
	}

	/**
	 * @return array
	 */
	public function formatArray()
	{
		$row = $this->_row;
		if (!empty($this->baseFormatter['includeBelongsTo'])) {
			if ($this->belongs_to) {
				foreach ($this->belongs_to as $k => $v) {
					if (!isset($parent) || $k !== $parent) {
						$model = $this->$k;
						$row[$k] = $model->loaded() ? $model->toArray() : null;
					}
					unset($row[$v['key']]);
				}
			}
		}

		return $row;
	}

	public function beginTransaction()
	{
		$this->conn->execute('START TRANSACTION');
	}

	public function rollback()
	{
		$this->conn->execute('ROLLBACK');
	}

	public function commit()
	{
		$this->conn->execute('COMMIT');
	}

	/**
	 *
	 * @inheritdoc
	 *
	 * Don't override this method if you want to add your custom delete scenario.
	 * Override deleteInternal() instead.
	 * And don't forget to call parent::deleteInternal() only if you are not going to completely replace it.
	 *
	 * @throws \Exception
	 */
	public function delete()
	{
		try {

			$this->beginTransaction();
			$this->deleteInternal();
			$this->commit();

		} catch (\Exception $e) {

			$this->rollback();
			throw $e;

		}
	}

	/**
	 * @throws \Exception
	 */
	protected function deleteInternal()
	{
		if (!$this->loaded()) {
			throw new \Exception("Cannot delete an item that wasn't selected from database");
		}

		if ($this->has_one) {
			foreach ($this->has_one as $k => $v) {
				if (!empty($v['cascade_delete'])) {
					$child = $this->$k;
					if ($child->loaded()) {
						$child->deleteInternal();
					}
				}
			}
		}

		if ($this->belongs_to) {
			foreach ($this->belongs_to as $k => $v) {
				if (!empty($v['cascade_delete'])) {
					$child = $this->$k;
					if ($child->loaded()) {
						$child->deleteInternal();
					}
				}
			}
		}

		if ($this->has_many) {
			foreach ($this->has_many as $field => $rel) {
				if (!empty($rel['cascade_delete'])) {
					if (!isset($rel['through'])) {
						foreach ($this->$field->find_all() as $model) {
							if ($model->loaded()) {
								$model->deleteInternal();
							}
						}
					} else {
						$this->pixie->db->query('delete')->table($rel['through'])
							->where($rel['key'], $this->id());
					}
				}

			}
		}

		$this->deleteSelf();
		$this->cached = array();
	}

	protected function deleteSelf()
	{
		if (!isset($this->_row[$this->id_field])) {
			throw new \Exception("Unknown ID field in model for table " . $this->table);
		}

		$this->conn->query('delete')
			->table($this->table)
			->where($this->id_field, $this->_row[$this->id_field])
			->execute();
	}

	/**
	 * @param array $config
	 * @return AbstractFormatter
	 * @throws \Exception
	 */
	protected function buildFormatterFromConfig($config)
	{
		if (!$config) {
			throw new \Exception('Empty formatter config');
		}

		if (is_array($config)) {
			if (!isset($config['class'])) {
				throw new \Exception('"class" is required field for formatter config');
			}

			$class = $config['class'];
			return new $class($this, $config);
		} else {
			$class = $config;
			return new $class($this, []);
		}
	}

	protected function overwriteReplaceRelationValues($values, $overwriteOptions, $relationOptions)
	{
		$ids = [];
		if (isset($overwriteOptions['extract_primary_method'])) {
			$methodName = $overwriteOptions['extract_primary_method'];
			foreach ($values as $value) {
				$ids[] = call_user_func([$this, $methodName], $value);
			}
		} else {
			foreach ($values as $v) {
				$ids[] = $v->id;
			}
		}

		$this->pixie->db->begin_transaction();

		try {
			if ($this->loaded()) {
				$this->conn->query('delete')
					->table($relationOptions['through'])
					->where($relationOptions['key'], $this->id())
					->execute();
			}

			if (!empty($overwriteOptions['ordering'])) {
				$orderedColumn = (isset($overwriteOptions['order_column'])) ? $overwriteOptions['order_column'] : 'order';
				foreach ($ids as $num => $id) {
					$this->conn->query('insert')
						->table($relationOptions['through'])
						->data([
							$relationOptions['foreign_key'] => $id,
							$relationOptions['key'] => $this->id(),
							$orderedColumn => $num,
						])
						->execute();
				}
			} else {
				foreach ($ids as $num => $id) {
					$this->conn->query('insert')
						->table($relationOptions['through'])
						->data([
							$relationOptions['foreign_key'] => $id,
							$relationOptions['key'] => $this->id(),
						])
						->execute();
				}
			}

			$this->pixie->db->commit();
		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			throw $e;
		}
	}
}