<?php

namespace Opake\ActivityLogger\Formatter\Booking\File;

use Opake\ActivityLogger\DefaultFormatter;

class ChangesFormatter extends DefaultFormatter
{
	protected function getLabels()
	{
		return [
			'name' => 'Name'
		];
	}
}