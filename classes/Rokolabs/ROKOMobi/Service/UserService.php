<?php

namespace Rokolabs\ROKOMobi\Service;

use Rokolabs\ROKOMobi\Client;
use Rokolabs\ROKOMobi\Dto\OrganizationDto;
use Rokolabs\ROKOMobi\Dto\UserDto;
use Rokolabs\ROKOMobi\Exception\BadApiStatusCodeException;
use Rokolabs\ROKOMobi\Helper\DtoNormalizer;
use Rokolabs\ROKOMobi\Result\Organization;
use Rokolabs\ROKOMobi\Result\User;

class UserService
{
    use ResponseAwareTrait;

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
     * @return User
     */
    public function createUser(UserDto $userDto)
    {
        $result = $this->client->post('users', DtoNormalizer::convertToArray($userDto));
        $json = $result->asJSON();
        $this->verifyResponseStatusCode($json);

        return $this->getUser($json->data->objectId);
    }

    /**
     * @param string $id
     * @return User
     * @throws BadApiStatusCodeException
     */
    public function getUser($id)
    {
        $result = $this->client->get('users/' . $id, [
            'resolve' => 'organizations'
        ]);
        $json = $result->asJSON();
        $this->verifyResponseStatusCode($json);

        return User::parse($json->data);
    }

    /**
     * @param string $id
     * @param UserDto $userDto
     */
    public function updateUserData($id, UserDto $userDto)
    {
        $this->client->put(
            sprintf('users/%s', $id),
            DtoNormalizer::convertToArray($userDto)
        );
    }

    /**
     * @param OrganizationDto $organizationDto
     * @return Organization
     * @throws BadApiStatusCodeException
     */
    public function createOrganization(OrganizationDto $organizationDto)
    {
        $result = $this->client->post('organizations', DtoNormalizer::convertToArray($organizationDto));
        $json = $result->asJSON();
        $this->verifyResponseStatusCode($json);

        return $json->data->objectId;
    }

    /**
     * @param string $userId
     * @param string $organizationIds
     */
    public function addOrganizationsToUser($userId, $organizationIds)
    {
        if (empty($organizationIds)) {
            return;
        }

        $this->client->post(
            sprintf('users/%s/organizations', $userId),
            $organizationIds
        );
    }
}
