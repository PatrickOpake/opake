<?php

namespace Rokolabs\ROKOMobi\Result;

class ContentGroup
{
    private $objectId;

    /**
     * ContentGroup constructor.
     * @param $objectId
     */
    public function __construct($objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * @return mixed
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param stdClass $data
     * @return ContentGroup
     */
    public static function parse($data)
    {
        return new ContentGroup($data->objectId);
    }
}
