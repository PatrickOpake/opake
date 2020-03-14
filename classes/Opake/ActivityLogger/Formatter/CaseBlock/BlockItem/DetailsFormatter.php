<?php

namespace Opake\ActivityLogger\Formatter\CaseBlock\BlockItem;

use Opake\ActivityLogger\DefaultFormatter;

class DetailsFormatter extends DefaultFormatter
{
	protected function getIgnored()
	{
		return ['block_item'];
	}

	protected function getLabels()
	{
		return [
			'block' => 'Block',
			'block_item' => 'Case Block',
		];
	}
}
