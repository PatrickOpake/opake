<?php

namespace OpakeAdmin\Form\Settings\BookingSheetTemplate;

use Opake\Form\AbstractForm;

class AssignForm extends AbstractForm
{
	/**
	 * @return array
	 */
	protected function getFields()
	{
		return [
			'is_all_sites',
		    'sites'
		];
	}
}