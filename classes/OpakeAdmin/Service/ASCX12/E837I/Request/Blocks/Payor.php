<?php

namespace OpakeAdmin\Service\ASCX12\E837I\Request\Blocks;

class Payor extends \OpakeAdmin\Service\ASCX12\E837\Request\Blocks\Payor
{

	protected function getInsuranceCompanyCode()
	{
		$code = $this->codingInsurance->getUB04PayerId();
		if (!$code) {
			throw new \Exception('The insurance company has no Electronic UB04 Payer ID code');
		}

		return $code;
	}

}