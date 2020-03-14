<?php

namespace Rokolabs\ROKOMobi\ClientParams;

class UploadFile
{
    /**
     * Resource retuned by fopen
     * @var resource
     */
    private $resource;

    /**
     * File size int bytes
     * @var int
     */
    private $size;

    /**
     * @param resource $resource
     * @param int $size
     */
    public function __construct($resource, $size)
    {
        $this->resource = $resource;
        $this->size = $size;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
}
