<?php

namespace Opake\Service;

/**
 * Абстрактный сервис
 */
abstract class AbstractService
{

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;
	protected $orm;
	protected $db;

	protected $base_model;

	public function __construct($pixie)
	{
		$this->pixie = $pixie;
		$this->orm = $pixie->orm;
		$this->db = $pixie->db;
	}

	public function beginTransaction()
	{
		$this->db->get()->execute('START TRANSACTION');
	}

	public function rollback()
	{
		$this->db->get()->execute('ROLLBACK');
	}

	public function commit()
	{
		$this->db->get()->execute('COMMIT');
	}

	public function getItem($id = null)
	{
		return $this->orm->get($this->base_model, $id);
	}

	/**
	 * Возвращает текущего пользователя системы
	 * @return \Opake\Model\User
	 */
	public function getUser()
	{
		return $this->pixie->auth->user();
	}

	public function check_access($section, $action, $model = null)
	{
		return $this->pixie->permissions->checkAccess($section, $action, $model);
	}

	public function getList($model = null, $org_id = null, $pages = null)
	{
		$model = $this->orm->get($model ? $model : $this->base_model);
		if ($org_id) {
			$model->where('organization_id', $org_id);
		}
		if ($pages) {
			$model->pagination($pages);
		}
		if (isset($model->name)) {
			$model->order_by('name', 'asc');
		}
		return $model->find_all();
	}

	public function getCount($model = null, $org_id = null)
	{
		$model = $this->orm->get($model ? $model : $this->base_model);
		if ($org_id) {
			$model->where('organization_id', $org_id);
		}
		return $model->count_all();
	}

	public function updateList($old, $new, $col = 'id')
	{
		$old_list = [];
		foreach ($old as $item) {
			$old_list[$item->$col] = $item;
		}
		foreach ($new as $item) {
			if (isset($old_list[$item->$col])) {
				unset($old_list[$item->$col]);
			}
			if (!$item->loaded()) {
				$item->save();
			}
		}
		foreach ($old_list as $item) {
			$item->delete();
		}
	}

}
