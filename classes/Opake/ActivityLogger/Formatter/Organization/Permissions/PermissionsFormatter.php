<?php

namespace Opake\ActivityLogger\Formatter\Organization\Permissions;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;

class PermissionsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		return FormatterHelper::formatOnOff($value);
	}

	protected function getLabels()
	{
		return [
			'inventory' => 'Inventory',
			'inventory.preference_cards' => 'Preference Cards',
			'billing' => 'Billing',
			'cm' => 'Case Management',
			'cm.intake' => 'Intake',
			'cm.pre_op' => 'Pre-Op',
			'cm.operation' => 'Operation',
			'cm.post_op' => 'Post-Op',
			'cm.operative_reports' => 'Operative Reports',
			'cm.dragon_dictation' => 'Dragon Dictation ',
			'cm.discharge' => 'Discharge'
		];
	}
}
