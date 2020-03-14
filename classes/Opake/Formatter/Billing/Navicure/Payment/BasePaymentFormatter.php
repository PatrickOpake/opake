<?php

namespace Opake\Formatter\Billing\Navicure\Payment;

use Opake\Formatter\BaseDataFormatter;

abstract class BasePaymentFormatter extends BaseDataFormatter
{
	protected function formatMoney($name, $options, $model)
	{
		return $this->_formatFloatToMoney($model->$name);
	}

	protected function _formatFloatToMoney($float)
	{
		return '$' . number_format((float) $float, 2, '.', ',');
	}
}