<?php

namespace Opake\Service;

class Settings extends AbstractService
{

	protected $base_model = 'Department';

	public function getItems($model)
	{
		$model = $this->orm->get($model);
		if (isset($model->active)) {
			$model->where('active', true);
		}
		return $model->find_all();
	}

	public function getDepartments()
	{
		$model = $this->orm->get('Department');
		$model->where('active', true);
		$model->order_by('name', 'asc');

		return $model->find_all();
	}

	public function updateBlockInfo($key, $data)
	{
		$this->beginTransaction();
		try {
			$this->db->query('delete')->table('block_info')->where('key', $key)->execute();
			$this->db->query('insert')->table('block_info')->data(['key' => $key, 'data' => $data])->execute();
		} catch (\Exception $e) {
			$this->rollback();
		}
		$this->commit();
	}

	public function getBlockInfo($key)
	{
		return $this->db->query('select')
			->table('block_info')
			->where('key', $key)
			->execute()
			->get('data');
	}
}
