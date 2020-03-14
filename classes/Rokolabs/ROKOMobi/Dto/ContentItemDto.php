<?php

namespace Rokolabs\ROKOMobi\Dto;

class ContentItemDto
{
    /**
     * @var int
     */
    public $objectId;

    /**
     * @var string
     */
    public $name;

    /**
     * @optional
     * @var array
     */
    public $customProperties;

    /**
     * @optional
     * @var string
     */
    public $description;

    /**
     * @optional
     * @var AssetDto[]
     */
    public $assets;

    /**
     * @optional
     * draft|pending|active
     * @var string
     */
    public $status;

    /**
     * @optional
     * @var string
     */
    public $date;
}
