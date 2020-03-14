<?php

namespace Rokolabs\ROKOMobi\Result;

use DateTime;
use Rokolabs\ROKOMobi\Helper\ResponseParser;
use stdClass;

class UserSession
{
    /**
     * @var string
     */
    private $sessionKey;

    /**
     * @var DateTime
     */
    private $sessionExpirationDate;

    /**
     * @var User
     */
    private $user;

    /**
     * @param string $sessionKey
     * @param DateTime $sessionExpirationDate
     * @param User $user
     */
    public function __construct($sessionKey, DateTime $sessionExpirationDate, User $user)
    {
        $this->sessionKey = $sessionKey;
        $this->sessionExpirationDate = $sessionExpirationDate;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getSessionKey()
    {
        return $this->sessionKey;
    }

    /**
     * @return DateTime
     */
    public function getSessionExpirationDate()
    {
        return $this->sessionExpirationDate;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param stdClass $response
     * @return UserSession
     */
    public static function parse($response)
    {
        $user = User::parse($response->user);
        $expiration = ResponseParser::parseDate($response->sessionExpirationDate);

        return new UserSession(
            $response->sessionKey,
            $expiration,
            $user
        );
    }
}

