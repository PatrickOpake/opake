<?php

namespace Rokolabs\ROKOMobi;

class Credential
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $masterApiKey;

    /**
     * @var string
     */
    private $apiBaseUrl;

    /**
     * @param string $apiKey
     * @param string $apiMasterKey
     * @param string $apiBaseUrl
     */
    public function __construct($apiKey, $apiMasterKey, $apiBaseUrl)
    {
        $this->apiKey = $apiKey;
        $this->masterApiKey = $apiMasterKey;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getMasterApiKey()
    {
        return $this->masterApiKey;
    }

    /**
     * @return string
     */
    public function getApiBaseUrl()
    {
        return $this->apiBaseUrl;
    }
}
