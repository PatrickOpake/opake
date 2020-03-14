<?php

namespace Rokolabs\ROKOMobi\Result;

class User
{
    /**
     * @var int
     */
    private $objectId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var Organization[]
     */
    private $organizations = [];

    /**
     * @param int $objectId
     * @param string $username
     * @param string $email
     */
    public function __construct($objectId, $username, $email, array $organizations)
    {
        $this->objectId = $objectId;
        $this->username = $username;
        $this->email = $email;
        $this->organizations = $organizations;
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return Organization[]
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }

    /**
     * @param string $organizationId
     * @return bool
     */
    public function isInOrganization($organizationId)
    {
        foreach ($this->organizations as $organization) {
            if ($organization->getObjectId() == $organizationId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param stdClass $data
     * @return User
     */
    public static function parse($data)
    {
        $email = !empty($data->email) ? $data->email : null;

        $organizations = [];
        if (!empty($data->organizations) && is_array($data->organizations)) {
            foreach ($data->organizations as $organization) {
                $organizations[] = Organization::parse($organization);
            }
        }

        return new User(
            $data->objectId,
            $data->username,
            $email,
            $organizations
        );
    }
}

