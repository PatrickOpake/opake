<?php

namespace OpakeAdmin\Service\Navicure\Claims;

use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Billing\Navicure\Log;
use OpakeAdmin\Service\Navicure\Claims\SFTP\Agent;

abstract class ClaimGenerator
{

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var array
	 */
	protected $collectionOfBills;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function __construct(\Opake\Model\Cases\Item $case, $collectionOfBills = [])
	{
		$this->case = $case;
		$this->collectionOfBills = $collectionOfBills;
	}

	protected function createNewAgent()
	{
		$agent = new Agent();
		$caseSite = $this->case->location->site;
		$agent->setUsernameAndPassword($caseSite->navicure_sftp_username, $caseSite->navicure_sftp_password);
		$agent->connect();

		return $agent;
	}

	protected function hasUnresolvedClaimForType($type)
	{
		$app = \Opake\Application::get();
		$count = $app->orm->get('Billing_Navicure_Claim')
			->where('case_id', $this->case->id())
			->where('type', $type)
			->where('active', 1)
			->where('and', [
				['and', ['status', '!=', Claim::STATUS_REJECTED_BY_PAYER]],
				['and', ['status', '!=', Claim::STATUS_REJECTED_BY_PROVIDER]],
				['and', ['status', '!=', Claim::STATUS_PAYMENT_DENIED]],
			])
			->count_all();

		return ($count > 0);
	}

	public static function splitClaims($case)
	{
		$proceduresSplitter = new ProceduresSplitter($case);
		return $proceduresSplitter->makeClaimProcedureContainers();
	}

	/**
	 * @return array
	 */
	abstract public function getCaseErrors();

	/**
	 * @return mixed
	 */
	abstract public function tryToSendClaim();

	/**
	 * @return mixed
	 */
	abstract public function hasUnresolvedClaim();
}