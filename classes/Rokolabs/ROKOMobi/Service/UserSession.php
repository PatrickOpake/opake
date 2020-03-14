<?php

namespace Rokolabs\ROKOMobi\Service;

use Rokolabs\ROKOMobi\Client;
use Rokolabs\ROKOMobi\Dto\UserDto;
use Rokolabs\ROKOMobi\Helper\DtoNormalizer;
use Rokolabs\ROKOMobi\Result\UserSession as UserSessionData;

class UserSession
{
    /**
     * @var UserSessionData
     */
    private $sessionData;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param UserDto $userDto
     * @return UserSessionData
     */
    public function start(UserDto $userDto)
    {
        $result = $this->client->post('usersession/setUserCmd', DtoNormalizer::convertToArray($userDto));

        $json = $result->asJSON();

        if (!$json) {
            return;
        }

        $this->sessionData = UserSessionData::parse($json->data);

        return $this->sessionData;
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return !empty($this->sessionData);
    }

    /**
     * @return UserSessionData
     */
    public function getSessionData()
    {
        return $this->sessionData;
    }
}
