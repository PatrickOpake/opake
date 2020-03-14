<?php

namespace Rokolabs\ROKOMobi\Dto;

class ViewerLinkDto
{
    /**
     * @var AssetDto
     */
    public $asset;

    /**
     * @var string
     */
    public $watermarkMode = "text";

    /**
     * @var string
     */
    public $watermarkValue = "watermark text";

    /**
     * @var string
     */
    public $watermarkPosition = "diagonal";

    /**
     * @var string
     */
    public $watermarkFontFamily = "Arial";

    /**
     * @var int
     */
    public $watermarkTextColor = 255;

    /**
     * @var int
     */
    public $watermarkTextSize = 25;

    /**
     * @var int
     */
    public $resolution = 120;

    /**
     * @var bool
     */
    public $allowPrint = true;

    /**
     * @var bool
     */
    public $allowDownload = true;
}
