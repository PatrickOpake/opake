<?php

namespace Rokolabs\ROKOMobi\Helper;

class DtoNormalizer
{
    /**
     * @param stdClass $stdClass
     * @return array
     */
    public static function convertToArray($stdClass)
    {
        $array = (array) $stdClass;

        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $array[$key] = self::convertToArray($value);
            }

            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $array[$key][$subKey] = self::convertToArray($subValue);
                }
            }
        }

        return $array;
    }
}