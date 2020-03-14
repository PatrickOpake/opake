<?php

namespace Rokolabs\ROKOMobi\Result;

use DateTime;
use Rokolabs\ROKOMobi\Helper\ResponseParser;
use stdClass;

class Organization
{
    /**
     * @var string
     */
    private $objectId;

    /**
     * @var DateTime
     */
    private $createdDate;

    /**
     * @var DateTime
     */
    private $updateDate;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $objectId
     * @param DateTime $createdDate
     * @param DateTime $updateDate
     * @param string $name
     * @param string $description
     */
    public function __construct($objectId, DateTime $createdDate, DateTime $updateDate, $name, $description)
    {
        $this->objectId = $objectId;
        $this->createdDate = $createdDate;
        $this->updateDate = $updateDate;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @return DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @return DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
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
     * @param stdClass $data
     * @return Organization
     */
    public static function parse($data)
    {
        $description = !empty($data->description) ? $data->description : '';

        return new Organization(
            $data->objectId,
            ResponseParser::parseDate($data->createDate),
            ResponseParser::parseDate($data->updateDate),
            $data->name,
            $description
        );
    }
}

