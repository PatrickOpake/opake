<?php

namespace Opake\ActivityLogger\Action\PrefCard;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Comparer\PrefCardComparer;
use Opake\ActivityLogger\Extractor\PrefCard\PrefCardExtractor;

class PrefCardChangeAction extends ModelAction
{

	protected function fetchDetails()
	{
		$card = $this->getExtractor()->getModel();
		$details =  [];
		$details['id'] = $card->id();
		if ($this->hasStaffCard()) {
			$details['card'] = 'staff';
			$details['user'] = $card->user_id;
		}
		if ($this->hasLocationCard()) {
			$details['card'] = 'location';
			$details['room'] = $card->location_id;
		}
		return $details;
	}

	protected function getFieldsForCompare()
	{
		return [
			'case_type_id',
			'user_id',
			'location_id',
			'items',
			'notes'
		];
	}

	protected function createComparer()
	{
		return new PrefCardComparer();
	}

	protected function createExtractor()
	{
		return new PrefCardExtractor();
	}

	protected function hasStaffCard()
	{
		$card = $this->getExtractor()->getModel();
		return ($card instanceof \Opake\Model\PrefCard\Staff);
	}

	protected function hasLocationCard()
	{
		$card = $this->getExtractor()->getModel();
		return ($card instanceof \Opake\Model\PrefCard\Location);
	}
}