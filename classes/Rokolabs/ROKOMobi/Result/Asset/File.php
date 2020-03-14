<?php

namespace Rokolabs\ROKOMobi\Result\Asset;

use DateTime;
use Rokolabs\ROKOMobi\Helper\ResponseParser;
use stdClass;

class File
{
    /**
     * @var int
     */
    private $objectId;

    /**
     * @var string
     */
    private $url;

    /**
     * @var DateTime|null
     */
    private $urlExpiresAt;

    /**
     * @param int $objectId
     * @param string $url
     * @param DateTime|null $urlExpiresAt
     */
    public function __construct($objectId, $url, DateTime $urlExpiresAt = null)
    {
        $this->objectId = $objectId;
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
     * @param stdClass $data
     * @return File
     */
    public static function parse($data)
    {
        return new File(
            $data->objectId,
            $data->url,
            !empty($data->urlExpiresAt) ? ResponseParser::parseDate($data->urlExpiresAt) : null
        );
    }
}
