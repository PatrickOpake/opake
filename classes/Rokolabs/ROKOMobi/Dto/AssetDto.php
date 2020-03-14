<?php
/**
 * Created by PhpStorm.
 * User: MagnusKan
 * Date: 20.11.2015
 * Time: 23:05
 */

namespace Rokolabs\ROKOMobi\Dto;


class AssetDto
{
    /**
     * @var string
     */
    public $name;

    /**
     * @optional
     * @var string
     */
    public $description;

    /**
     * @var FileDto
     */
    public $file;

    /**
     * @optional
     * @var string
     */
    public $fileName;

    /**
     * @var AssetTypeDto
     */
    public $assetType;
}
