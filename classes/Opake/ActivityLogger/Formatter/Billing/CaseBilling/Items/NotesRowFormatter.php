<?php

namespace Opake\ActivityLogger\Formatter\Billing\CaseBilling\Items;

use Opake\ActivityLogger\Formatter\ArrayRowFormatter;

class NotesRowFormatter extends ArrayRowFormatter
{
	protected function formatLabel($id)
	{
		return 'Note #' . $id;
	}

	protected function getLabels()
	{
		return [
			'note' => 'Text',
		];
	}
}