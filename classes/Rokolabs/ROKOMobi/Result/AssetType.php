<?php
/**
 * Created by PhpStorm.
 * User: MagnusKan
 * Date: 21.11.2015
 * Time: 15:15
 */

namespace Rokolabs\ROKOMobi\Result;


class AssetType
{
    const PRIMARY = 'Primary';

    /**
     * @var int
     */
    private $objectId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isUnique = false;

    /**
     * @var bool
     */
    private $isBuiltIn = false;

    /**
     * @param int $objectId
     * @param string $name
     * @param bool $isUnique
     * @param bool $isBuiltIn
     */
    public function __construct($objectId, $name, $isUnique, $isBuiltIn)
    {
        $this->objectId = $objectId;
        $this->name = $name;
        $this->isBuiltIn = $isBuiltIn;
        $this->isUnique = $isUnique;
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
     * @return boolean
     */
    public function isUnique()
    {
        return $this->isUnique;
    }

    /**
     * @return boolean
     */
    public function isBuiltIn()
    {
        return $this->isBuiltIn;
    }

    /**
     * @return bool
     */
    public function isPrimary()
    {
        return $this->name == self::PRIMARY;
    }

    /**
     * @param stdClass $data
     * @return AssetType
     */
    public static function parse($data)
    {
        return new AssetType(
            $data->objectId,
            $data->name,
            $data->isUnique,
            $data->isBuiltIn
        );
    }
}
