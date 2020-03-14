<?php

namespace Rokolabs\ROKOMobi\Result;

use Rokolabs\ROKOMobi\Result\Asset\File;
use stdClass;

class Asset
{
    /**
     * @var int
     */
    private $objectId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var File
     */
    private $file;

    /**
     * @var AssetType
     */
    private $assetType;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @param int $objectId
     * @param string $name
     * @param File $file
     * @param AssetType $assetType
     * @param string $fileName
     */
    public function __construct(
        $objectId,
        $name,
        File $file,
        AssetType $assetType,
        $fileName
    ) {
        $this->objectId = $objectId;
        $this->name = $name;
        $this->file = $file;
        $this->assetType = $assetType;
        $this->fileName = $fileName;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return AssetType
     */
    public function getAssetType()
    {
        return $this->assetType;
    }

    /**
     * @param stdClass $data
     * @return Asset
     */
    public static function parse($data)
    {
        return new Asset(
            $data->objectId,
            $data->name,
            File::parse($data->file),
            AssetType::parse($data->assetType),
            !empty($data->fileName) ? $data->fileName : ''
        );
    }
}
