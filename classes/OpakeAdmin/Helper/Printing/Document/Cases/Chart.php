<?php
namespace OpakeAdmin\Helper\Printing\Document\Cases;

use Opake\Model\UploadedFile;
use OpakeAdmin\Helper\Printing\Document;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart\ChartFile;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart\ChartFileWithHeader;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart\ChartOwnText;

abstract class Chart extends Document
{
	const A4_LANDSCAPE = 297;
	const A4_PORTRAIT = 210;

	/**
	 * @param $form
	 * @param null $case
	 * @return ChartFile|ChartFileWithHeader|ChartOwnText
	 */
	public static function createDocument($form, $case = null)
	{
		if ($form->uploaded_file_id && $form->file->loaded()) {
			if ($form->include_header) {
				return new ChartFileWithHeader($form, $case);
			}
			return new ChartFile($form, $case);
		} else {
			return new ChartOwnText($form, $case);
		}
	}

	public static function getProportionPixelWidth($documentWidth)
	{
		return round(($documentWidth * 1096) / static::A4_PORTRAIT);
	}
}