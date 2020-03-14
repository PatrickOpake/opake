<?php

namespace OpakeApi\Controller;

class Cases extends AbstractController
{

	public function actionMycases()
	{
		$search = new \OpakeApi\Model\Search\Cases($this->pixie);
		$results = $search->search($this->request);

		$cases = [];
		foreach ($results as $case) {
			$cases[] = $case->toShortArray();
		}
		$this->result = ['cases' => $cases];
	}

	public function actionMycasesStartTimes()
	{
		$search = new \OpakeApi\Model\Search\Cases($this->pixie);
		$results = $search->searchStartTimes($this->request);

		$times = [];
		foreach ($results as $time) {
			$times[] = date('Y-m-d H:i:s O', strtotime($time));
		}
		$this->result = $times;
	}

	public function actionCase()
	{
		$case = $this->loadModel('Cases_Item', 'caseid');
		if (!$case->loaded() || $case->organization_id != $this->logged()->organization_id) {
			throw new \OpakeApi\Exception\PageNotFound();
		}
		$this->result = $case->toArray();
	}

	public function actionSurgerytypes()
	{
		$types = [];
		foreach ($this->services->get('settings')->getItems('Cases_Type') as $type) {
			$types[] = $type->toArray();
		}

		$this->result = new \stdClass();
		$this->result->types = $types;
	}

	public function actionIcds()
	{
		$icds = [];
		$model = $this->orm->get('ICD');

		$offset = filter_input(INPUT_GET, 'offset', FILTER_VALIDATE_INT);
		$limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);

		if ($offset) {
			$model->offset($offset);
		}
		if ($limit) {
			$model->limit($limit);
		}

		foreach ($model->find_all() as $icd) {
			$icds[] = $icd->toArray();
		}

		$this->result = new \stdClass();
		$this->result->icds = $icds;
	}

}
