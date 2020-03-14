<?php
namespace Opake\Model\Billing\Navicure;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

class Log extends AbstractModel
{
	const TRANSACTION_837 = 1;
	const TRANSACTION_997 = 2;
	const TRANSACTION_277 = 3;
	const TRANSACTION_835 = 4;
	const TRANSACTION_837I = 5;

	const DIRECTION_OUT = 1;
	const DIRECTION_IN = 2;

	public $id_field = 'id';

	public $table = 'billing_navicure_log';

	protected $_row = [
		'id' => null,
	    'claim_id' => null,
	    'transaction'=> null,
		'direction' => null,
	    'status' => null,
	    'error' => null,
	    'data' => null,
	    'time' => null,
	];

	protected $belongs_to = [
		'claim' => [
			'model' => 'Billing_Navicure_Claim',
			'key' => 'claim_id'
		],
	];

	public function getTransactionName()
	{
		if ($this->transaction) {
			if ($this->transaction == Log::TRANSACTION_837) {
				return '837-P';
			}
			if ($this->transaction == Log::TRANSACTION_837I) {
				return '837-I';
			}
			if ($this->transaction == Log::TRANSACTION_835) {
				return '835';
			}
			if ($this->transaction == Log::TRANSACTION_277) {
				return '277';
			}
			if ($this->transaction == Log::TRANSACTION_997) {
				return '997';
			}
		}

		return null;
	}

	public function toArray()
	{
		$transactionDescription = '';
		if ($this->direction) {
			if ($this->direction == Log::DIRECTION_IN) {
				$transactionDescription .= '← ';
			}
			if ($this->direction == Log::DIRECTION_OUT) {
				$transactionDescription .= '→ ';
			}
		}

		$transactionDescription .= $this->getTransactionName();

		$time = TimeFormat::fromDBDatetime($this->time);

		$caseId = ($this->claim->loaded()) ? $this->claim->case_id : 0;

		return [
			'id' => (int) $this->id(),
		    'claim_id' => (int) $this->claim_id,
		    'case_id' => (int) $caseId,
		    'transaction_description' => $transactionDescription,
		    'time' => TimeFormat::formatToJsDate($time),
		    'error' => $this->error
		];
	}
}