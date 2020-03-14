<?php

namespace OpakeAdmin\Controller\Billings\BatchEligibility;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionCases()
	{
		$items = [];
		$search = new \OpakeAdmin\Model\Search\Billing\BatchEligibility($this->pixie, false);
		$search->setOrganizationId($this->org->id());
		$results = $search->search(
			$this->orm->get('Cases_Registration_Insurance'),
			$this->request
		);

		foreach ($results as $result) {
			$items[] = $result
				->getFormatter('ListEntry')
				->toArray();
		}


		$this->result = $items;
	}

	public function actionItems()
	{
		$result = [];
		$model = $this->orm->get('Eligible_BatchCoverage')->where('organization_id', $this->org->id);

		foreach ($model->find_all() as $item) {
			$result[] = $item->toArray();
		}

		$this->result = $result;
	}

	public function actionViewDetail()
	{
		$result = [];
		$rows = $this->pixie->db->query('select')
			->table('case_batch_eligibility_cases')
			->fields('case_insurance_id')
			->where('batch_id', $this->request->param('subid'))
			->execute();

		$caseInsurancesId = [];
		foreach ($rows as $row) {
			$caseInsurancesId[] = $row->case_insurance_id;
		}

		$caseInsurances = $this->orm->get('Cases_Registration_Insurance')
			->where('id', 'IN', $this->pixie->db->arr($caseInsurancesId))
			->find_all();

		foreach ($caseInsurances as $item) {
			$result[] = $item->getFormatter('ListEntry')
				->toArray();
		}

		$this->result = $result;
	}


}
