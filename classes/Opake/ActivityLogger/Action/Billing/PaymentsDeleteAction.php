<?php

namespace Opake\ActivityLogger\Action\Billing;


use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Extractor\Billing\BillingPaymentExtractor;
use Opake\Model\Billing\Ledger\PaymentInfo;

class PaymentsDeleteAction extends ModelAction
{

	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		$paymentInfo = $model->payment_info;
		$case = $model->coding_bill->coding->case;

		$paymentSourceTitlesList = PaymentInfo::getPaymentSourcesList();
		$paymentSource =  (isset($paymentSourceTitlesList[$paymentInfo->payment_source])) ? $paymentSourceTitlesList[$paymentInfo->payment_source] : '';

		$paymentMethodTitlesList = PaymentInfo::getPaymentMethodsList();
		$paymentMethod =  (isset($paymentMethodTitlesList[$paymentInfo->payment_method])) ? $paymentMethodTitlesList[$paymentInfo->payment_method] : '';


		return [
			'case_id' => $case->id,
			'payment_date' => $paymentInfo->date_of_payment,
			'payment_source' => $paymentSource,
			'payment_method' => $paymentMethod,
			'payment_amount' => $model->amount,
			//'total_amount' => $paymentInfo->total_amount
		];
	}

	protected function getFieldsForCompare()
	{
		return [

		];
	}

	/**
	 * @return
	 */
	protected function createExtractor()
	{
		return new BillingPaymentExtractor();
	}
}