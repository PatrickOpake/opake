<?php

namespace Rokolabs\ROKOMobi;

class Response
{
    /**
     * @var string
     */
    private $data;

    /**
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param bool|false $assocArray
     * @return stdClass|array
     */
    public function asJSON($assocArray = false)
    {
        return json_decode($this->getData(), $assocArray);
    }
}
