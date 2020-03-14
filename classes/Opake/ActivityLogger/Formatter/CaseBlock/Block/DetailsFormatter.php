<?php

namespace Opake\ActivityLogger\Formatter\CaseBlock\Block;

use Opake\ActivityLogger\DefaultFormatter;

class DetailsFormatter extends DefaultFormatter
{
	protected function getLabels()
	{
		return [
			'block' => 'Block',
		];
	}
}
