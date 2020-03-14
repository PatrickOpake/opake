<?php

namespace Opake\ActivityLogger\Exporter;

class RichText extends \PHPExcel_RichText
{
	public function addRichText(RichText $text)
	{
		$currentElems = $this->getRichTextElements();
		foreach ($text->getRichTextElements() as $elem) {
			$currentElems[] = $elem;
		}

		$this->setRichTextElements($currentElems);
	}

	public function removeLastElement()
	{
		$currentElems = $this->getRichTextElements();
		if ($currentElems) {
			$currentElems = array_splice($currentElems, 0, count($currentElems) - 1);
			$this->setRichTextElements($currentElems);
		}

	}
}