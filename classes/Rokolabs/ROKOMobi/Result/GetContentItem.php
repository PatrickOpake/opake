<?php

namespace Rokolabs\ROKOMobi\Result;

use DateTime;
use Rokolabs\ROKOMobi\Helper\ResponseParser;
use stdClass;

class GetContentItem
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
     * @var string
     */
    private $description;

    /**
     * @var Asset[]
     */
    private $assets = [];

    /**
     * @var string
     */
    private $status;

    /**
     * @var DateTime
     */
    private $releaseDate;

    /**
     * @var DateTime
     */
    private $createDate;

    /**
     * @var DateTime
     */
    private $updateDate;

    /**
     * @param int $objectId
     * @param string $name
     * @param string $description
     * @param Asset[] $assets
     * @param string $status
     * @param DateTime $releaseDate
     * @param DateTime $createDate
     * @param DateTime $updateDate
     */
    public function __construct(
        $objectId,
        $name,
        $description,
        array $assets,
        $status,
        DateTime $releaseDate,
        DateTime $createDate,
        DateTime $updateDate
    ) {
        $this->objectId = $objectId;
        $this->name = $name;
        $this->description = $description;
        $this->assets = $assets;
        $this->status = $status;
        $this->releaseDate = $releaseDate;
        $this->createDate = $createDate;
        $this->updateDate = $updateDate;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Asset[]
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * @return Asset
     * @throws \Exception
     */
    public function getFirstAsset()
    {
        if (!$this->assets) {
            throw new \Exception('Asset array is empty');
        }

        return $this->assets[0];
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * @return DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @return DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @param stdClass $data
     * @return GetContentItem
     */
    public static function parse($data)
    {
        $assets = [];

        if (!empty($data->assets)) {
            foreach ($data->assets as $asset) {
                $assets[] = Asset::parse($asset);
            }
        }

        return new GetContentItem(
            $data->objectId,
            $data->name,
            isset($data->description) ? $data->description : '',
            $assets,
            $data->status,
            ResponseParser::parseDate($data->releaseDate),
            ResponseParser::parseDate($data->createDate),
            ResponseParser::parseDate($data->updateDate)
        );
    }
}
