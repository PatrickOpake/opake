<?php

namespace Opake\ActivityLogger\Formatter\Inventory\Item;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{

		switch ($key) {

			case 'manf_id':
				return FormatterHelper::formatVendor($this->pixie, $value);
			case 'auto_order':
				return FormatterHelper::formatYesNo($value);
			case 'is_remanufacturable':
				return FormatterHelper::formatYesNo($value);
			case 'is_resterilizable':
				return FormatterHelper::formatYesNo($value);
			case 'type':
				return FormatterHelper::formatInventoryType($this->pixie, $value);
			case 'image_id':
				return LinkFormatterHelper::formatUploadedFileLink($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'barcode' => 'Barcode',
			'hcpcs' => 'HCPCS',
			'auto_order' => 'Auto Order',
			'is_remanufacturable' => 'Remanufacturable',
			'is_resterilizable' => 'Resterilizable',
			'name' => 'Name',
			'manf_id' => 'Manufacturer',
			'desc' => 'Description',
			'type' => 'Type',
			'min_level' => 'Par Level Min',
			'max_level' => 'Par Level Max',
			'uom' => 'Unit of Measure',
			'qty_per_uom' => 'Qty/Unit',
			'price' => 'Unit Price',
			'image_id' => 'Image'
		];
	}
}