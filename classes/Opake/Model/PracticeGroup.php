<?php

namespace Opake\Model;

class PracticeGroup extends AbstractModel
{
	public $id_field = 'id';

	public $table = 'practice_groups';

	protected $_row = [
		'id' => null,
		'name' => '',
		'active' => true
	];

	public function getValidator()
	{
		$validator = parent::getValidator();
		$validator->field('name')->rule('filled')->error('Invalid Name');
		return $validator;
	}

	public function toArray()
	{
		return [
			'id' => (int) $this->id(),
			'name' => $this->name,
			'active' => (bool) $this->active
		];
	}

	public function toExpandedArray($orgId)
	{
		return [
			'id' => (int) $this->id(),
			'name' => $this->name,
			'fullname' => $this->name,
			'active' => (bool) $this->active,
			'case_color' => $this->getCaseColor($orgId)
		];
	}

	public function updateCaseColor($caseColor, $orgId)
	{
		$this->pixie->db->query('update')
			->table('organization_practice_groups')
			->data(['case_color' => $caseColor])
			->where([['organization_id', $orgId], ['practice_group_id', $this->id]])
			->execute();
	}

	public function getCaseColor($orgId)
	{
		if ($this->loaded()) {
			$query = $this->pixie->db->query('select')
				->table('organization_practice_groups')
				->fields('case_color')
				->where([['organization_id', $orgId], ['practice_group_id', $this->id]])
				->execute()
				->current();

			return $query->case_color;
		} else {
			return '';
		}
	}
}