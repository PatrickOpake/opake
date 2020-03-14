<?php

namespace OpakeAdmin\Controller\Clients\Internal;

use Opake\Model\Patient;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}
	}

	public function actionUsers()
	{
		$users = $this->pixie->orm->get('User');

		if ($this->request->get('profession')) {
			$profession = json_decode($this->request->get('profession'));
			if(is_array($profession)) {
				$users->where('profession_id', 'IN', $this->pixie->db->expr('(' . implode(', ', $profession) . ')'));

			} else {
				$users->where('profession_id', $profession);
			}
		}
		$users->where('status', \Opake\Model\User::STATUS_ACTIVE);

		$results = [];
		foreach ($users->find_all() as $user) {
			$results[] = $user->getFormatter('SelectOptions')->toArray();
		}

		$this->result = $results;
	}

	public function actionPatients()
	{
		$result = [];
		$patient = $this->orm->get('Patient');

		if ($q = $this->request->get('query')) {
			$patient->where('and', [
				['or', [$this->pixie->db->expr("CONCAT_WS(' ',first_name,last_name)"), 'like', '%' . $q . '%']],
				['or', [$this->pixie->db->expr("CONCAT_WS(' ',last_name,first_name)"), 'like', '%' . $q . '%']],
				['or', [$this->pixie->db->expr("CONCAT_WS(', ',first_name, last_name)"), 'like', '%' . $q . '%']],
				['or', [$this->pixie->db->expr("CONCAT_WS(', ',last_name,first_name)"), 'like', '%' . $q . '%']],
				['or', [$this->pixie->db->expr("CONCAT_WS(',',last_name,first_name)"), 'like', '%' . $q . '%']],
				['or', [$this->pixie->db->expr("CONCAT_WS(',',first_name,last_name)"), 'like', '%' . $q . '%']]
			]);
		}
		$patient->where('status', Patient::STATUS_ACTIVE)
			->order_by('first_name', 'asc')
			->order_by('last_name', 'asc')
			->limit(12);

		$search = new \OpakeAdmin\Model\Search\Patient($this->pixie, false);
		$results = $search->search($patient, $this->request);

		foreach ($results as $patient) {
			$result[] = $patient->toShortArray();
		}
		$this->result = $result;
	}

}