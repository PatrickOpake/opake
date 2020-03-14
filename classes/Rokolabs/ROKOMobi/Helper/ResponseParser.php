<?php

namespace Rokolabs\ROKOMobi\Helper;

use DateTime;

class ResponseParser
{
    const DATE_FORMAT = 'Y-m-d\TH:i:s';
    const API_STATUS_CODE_SUCCESS = 'Success';

    /**
     * @param string $dateString
     * @return DateTime
     */
    public static function parseDate($dateString)
    {
        $dateString = preg_replace('/\.\d+Z$/', '', $dateString);
        return DateTime::createFromFormat(self::DATE_FORMAT, $dateString);
    }

    /**
     * @param $json
     * @return bool
     */
    public static function isSuccessResponse($json)
    {
        return $json->apiStatusCode == self::API_STATUS_CODE_SUCCESS;
    }
}
