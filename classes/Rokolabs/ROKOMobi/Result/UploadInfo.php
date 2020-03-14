<?php

namespace Rokolabs\ROKOMobi\Result;

use DateTime;
use Rokolabs\ROKOMobi\Helper\ResponseParser;

class UploadInfo
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var DateTime|null
     */
    private $expiresAt;

    /**
     * @var string[]
     */
    private $headers;

    /**
     * @var string
     */
    private $httpMethod;

    /**
     * UploadInfo constructor.
     * @param string $url
     * @param DateTime|null $expiresAt
     * @param string[] $headers
     * @param string $httpMethod
     */
    public function __construct($url, $expiresAt, array $headers, $httpMethod)
    {
        $this->url = $url;
        $this->expiresAt = $expiresAt;
        $this->headers = $headers;
        $this->httpMethod = $httpMethod;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @return string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string[]
     */
    public function getFullHeaders()
    {
        $headers = [];
        foreach ($this->headers as $header => $value) {
            $headers[] = sprintf('%s: %s', $header, $value);
        }

        return $headers;
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @param stdClass $json
     * @return UploadInfo
     */
    public static function parse($json)
    {
        return new UploadInfo(
            $json->url,
            ResponseParser::parseDate($json->expiresAt),
            (array) $json->headers,
            $json->httpMethod
        );
    }
}
