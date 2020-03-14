<?php

namespace Rokolabs\ROKOMobi\Result;

class CreateContentItem
{
    /**
     * @var int
     */
    private $objectId;

    /**
     * @param $objectId
     */
    public function __construct($objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param stdClass $data
     * @return CreateContentItem
     */
    public static function parse($data)
    {
        return new CreateContentItem($data->objectId);
    }
}
