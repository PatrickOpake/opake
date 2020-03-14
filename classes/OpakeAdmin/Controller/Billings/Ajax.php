<?php

namespace OpakeAdmin\Controller\Billings;

use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;
use OpakeAdmin\Service\Navicure\Claims\ClaimGenerator;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{

		$items = [];

		$model = $this->orm->get('Cases_Item')
			->where('organization_id', $this->org->id)
			->where('and', [
				['or', ['appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED]],
				['or', ['is_remained_in_billing', 1]]
			]);

		$search = new \OpakeAdmin\Model\Search\Cases\Billing($this->pixie, $this->request->get('completed') === 'true');
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->getFormatter('BillingList')->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionCaseCodes()
	{
		$result = [];
		$q = $this->request->get('query');
		$usedCodesIds = explode(',', $this->request->get('used_codes_ids'));
		$caseId = $this->request->get('case_id');

		$case = $this->pixie->orm->get('Cases_Item', $caseId);
		if (!$case->loaded()) {
			throw new PageNotFound();
		}

		$siteId = $case->location->site_id;

		$master = $this->orm->get('Master_Charge')
			->where('site_id', $siteId)
			->where('archived', 0);

		if ($q !== null) {
			$master->where([
				['cpt', 'like', '%' . $q . '%']
			]);
		}

		if ($usedCodesIds) {
			$master->where('id', 'NOT IN', $this->pixie->db->arr($usedCodesIds));
		}

		$master->query->group_by('cpt');
		$master->order_by('cpt', 'asc')->limit(12);

		foreach ($master->find_all() as $item) {
			$result[] = $item->getFormatter('ListOption')->toArray();
		}

		$this->result = $result;
	}

	public function actionSendBulkClaims()
	{
		$data = $this->getData();

		if (empty($data->cases)) {
			throw new BadRequest('Bad Request');
		}

		$results = [];
		try {
			foreach ($data->cases as $caseId) {
				$case = $this->orm->get('Cases_Item', $caseId);
				if ($case->loaded()) {
					if ($case->organization_id == $this->org->id()) {
							if ($case->coding->is_ready_professional_claim) {

								try {

									$result = [
										'case_id' => $case->id(),
									    'type' => 'Electronic 1500',
									    'success' => true
									];

									$chunkedBills = ClaimGenerator::splitClaims($case);
									foreach ($chunkedBills as $chunkedBill) {
										$generator = new \OpakeAdmin\Service\Navicure\Claims\Generator\ProfessionalClaimGenerator($case, $chunkedBill);
										$generator->tryToSendClaim();
									}
									$results[] = $result;

									$case->coding->is_ready_professional_claim = false;
									$case->coding->save();

								} catch (\Exception $e) {
									$this->pixie->logger->exception($e);
									$result['success'] = false;
									$result['errors'] = [$e->getMessage()];
									$results[] = $result;
								}
							}

							if ($case->coding->is_ready_institutional_claim) {
								try {

									$result = [
										'case_id' => $case->id(),
										'type' => 'Electronic UB04',
										'success' => true
									];

									$case->coding->is_ready_institutional_claim = false;
									$case->coding->save();

									$chunkedBills = ClaimGenerator::splitClaims($case);
									foreach ($chunkedBills as $chunkedBill) {
										$generator = new \OpakeAdmin\Service\Navicure\Claims\Generator\InstitutionalClaimGenerator($case, $chunkedBill);
										$generator->tryToSendClaim();
									}
									$results[] = $result;

								} catch (\Exception $e) {
									$this->pixie->logger->exception($e);
									$result['success'] = false;
									$result['errors'] = [$e->getMessage()];
									$results[] = $result;
								}
							}
					}
				}
			}

			$this->result = [
				'success' => true,
			    'results' => $results
			];
		} catch (\Exception $e) {
			$this->pixie->logger->exception($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()],
			];
		}
	}

}
