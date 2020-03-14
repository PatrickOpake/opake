<?php

namespace Rokolabs\ROKOMobi\Result;

use DateTime;
use Rokolabs\ROKOMobi\Helper\ResponseParser;
use Rokolabs\ROKOMobi\Result\UploadInfo;

class CreateFile
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
    private $contentType;

    /**
     * @var UploadInfo
     */
    private $uploadInfo;

    /**
     * @var string
     */
    private $url;

    /**
     * @var DateTime|null
     */
    private $urlExpiresAt;

    /**
     * @var DateTime|null
     */
    private $createDate;

    /**
     * @var DateTime|null
     */
    private $updateDate;

    /**
     * @param int $objectId
     * @param string $name
     * @param string $contentType
     * @param UploadInfo $uploadInfo
     * @param string $url
     * @param DateTime|null $urlExpiresAt
     * @param DateTime|null $createDate
     * @param DateTime|null $updateDate
     */
    public function __construct(
        $objectId,
        $name,
        $contentType,
        UploadInfo $uploadInfo,
        $url,
        DateTime $urlExpiresAt = null,
        DateTime $createDate = null,
        DateTime $updateDate = null
    ) {
        $this->name = $name;
        $this->contentType = $contentType;
        $this->uploadInfo = $uploadInfo;
        $this->createDate = $createDate;
        $this->objectId = $objectId;
        $this->updateDate = $updateDate;
        $this->url = $url;
        $this->urlExpiresAt = $urlExpiresAt;
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
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return UploadInfo
     */
    public function getUploadInfo()
    {
        return $this->uploadInfo;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return DateTime|null
     */
    public function getUrlExpiresAt()
    {
        return $this->urlExpiresAt;
    }

    /**
     * @return DateTime|null
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @param stdClass $data
     * @return CreateFile
     */
    public static function parse($data)
    {
        return new CreateFile(
            $data->objectId,
            !empty($data->name) ? $data->name : '',
            !empty($data->contentType) ? $data->contentType : null,
            UploadInfo::parse($data->uploadInfo),
            $data->url,
            !empty($data->urlExpiresAt) ? ResponseParser::parseDate($data->urlExpiresAt) : null,
            ResponseParser::parseDate($data->createDate),
            ResponseParser::parseDate($data->updateDate)
        );
    }
}