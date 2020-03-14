<?php

namespace Rokolabs\ROKOMobi\Dto;

class AssetTypeDto
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
     * @var bool
     */
    public $isUnique;

    /**
     * @optional
     * @var array
     */
    public $contentGroups;
}
