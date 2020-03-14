<?php

namespace Rokolabs\ROKOMobi\Service;

use Rokolabs\ROKOMobi\Exception\BadApiStatusCodeException;
use Rokolabs\ROKOMobi\Helper\ResponseParser;

trait ResponseAwareTrait
{
    /**
     * @param \stdClass $json
     * @throws BadApiStatusCodeException
     */
    private function verifyResponseStatusCode($json)
    {
        if (!ResponseParser::isSuccessResponse($json)) {
            throw new BadApiStatusCodeException('Bad Api Status Code [' . $json->apiStatusCode . ']: ' . $json->apiStatusMessage);
        }
    }
}
