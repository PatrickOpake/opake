<?php

namespace Opake\Helper;


class Arrays
{

    /**
     * @param $array
     * @param $propName
     * @return array
     */
    public static function mapPropertyToKey($array, $propName)
    {
        $map = array();
        foreach ($array as $key => $value) {
            $map[$value[$propName]] = $key;
        }
        return $map;
    }

    /**
     * @param $array
     * @param $propName
     * @return array
     */
    public static function mapKeyToProperty($array, $propName)
    {
        $map = array();
        foreach ($array as $key => $value) {
            $map[$key] = $value[$propName];
        }
        return $map;
    }

    /**
     * @param $array
     * @param $propName
     * @return array
     */
    public static function reKeyByProperty($array, $propName)
    {
        $new = array();
        foreach ($array as $value) {
            $new[$value[$propName]] = $value;
        }
        return $new;
    }

    /**
     * Copy keys $properties from mixed to base
     *
     * @param array $base
     * @param array $mixed
     * @param array $properties
     * @param bool $copy_nulls copy nulls if no data for key or do not copy
     * @return $changed
     */
    public static function copyProperties(array $base, array $mixed, array $properties, $copy_nulls = true)
    {
        foreach ($properties as $prop_name) {
            if ($copy_nulls || isset($mixed[$prop_name])) {
                $base[$prop_name] = isset($mixed[$prop_name]) ? $mixed[$prop_name] : null ;
            }
        }
        return $base;
    }
}
