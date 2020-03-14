<?php

namespace Opake\ActivityLogger\Formatter\Intake\Forms;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{

	protected function getLabels()
	{
		return [
			'form' => 'Form',
		];
	}
}