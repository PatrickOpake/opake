<?php

namespace Rokolabs\ROKOMobi;

use Rokolabs\ROKOMobi\ClientParams\UploadFile;
use Rokolabs\ROKOMobi\Dto\ContentGroupDto;
use Rokolabs\ROKOMobi\Dto\ContentItemDto;
use Rokolabs\ROKOMobi\Dto\FileDto;
use Rokolabs\ROKOMobi\Dto\MergeAssetsDto;
use Rokolabs\ROKOMobi\Dto\UploadInfoDto;
use Rokolabs\ROKOMobi\Dto\ViewerLinkDto;
use Rokolabs\ROKOMobi\Exception\BadApiStatusCodeException;
use Rokolabs\ROKOMobi\Helper\DtoNormalizer;
use Rokolabs\ROKOMobi\Helper\ResponseParser;
use Rokolabs\ROKOMobi\Result\AssetType;
use Rokolabs\ROKOMobi\Result\ContentGroup;
use Rokolabs\ROKOMobi\Result\CreateContentItem;
use Rokolabs\ROKOMobi\Result\CreateFile;
use Rokolabs\ROKOMobi\Result\DocumentOperationBegin;
use Rokolabs\ROKOMobi\Result\DocumentOperationStatus;
use Rokolabs\ROKOMobi\Result\GetContentItem;
use Rokolabs\ROKOMobi\Service\ResponseAwareTrait;
use Rokolabs\ROKOMobi\Service\UserSession;

class ContentService
{
    use ResponseAwareTrait;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var UserSession
     */
    private $userSessionService;

    /**
     * @param Client $client
     * @param UserSession $userSession
     */
    public function __construct(Client $client, UserSession $userSession)
    {
        $this->client = $client;
        $this->userSessionService = $userSession;
    }

    /**
     * @param FileDto $fileDto
     * @return CreateFile
     * @throws BadApiStatusCodeException
     */
    public function createFile(FileDto $fileDto)
    {
        $response = $this->client->post(
            'files',
            DtoNormalizer::convertToArray($fileDto),
            $this->userSessionService->getSessionData()
        );
        $json = $response->asJSON();
        $this->verifyResponseStatusCode($json);

        return CreateFile::parse($json->data);
    }

    /**
     * @param UploadFile $uploadFile
     * @param UploadInfoDto $uploadInfoDto
     * @return bool
     */
    public function uploadFile(UploadFile $uploadFile, UploadInfoDto $uploadInfoDto)
    {
        $this->client->customUpload($uploadInfoDto->url, $uploadFile, $uploadInfoDto->headers);

        return true;
    }

    /**
     * @param ContentItemDto $contentItemDto
     * @return CreateContentItem
     * @throws BadApiStatusCodeException
     */
    public function createContentItem(ContentItemDto $contentItemDto)
    {
        $response = $this->client->post(
            'contentitems',
            DtoNormalizer::convertToArray($contentItemDto),
            $this->userSessionService->getSessionData()
        );
        $json = $response->asJSON();
        $this->verifyResponseStatusCode($json);

        return CreateContentItem::parse($json->data);
    }

    /**
     * @param int $id
     * @param string $resolve
     * @return GetContentItem
     * @throws BadApiStatusCodeException
     */
    public function getContentItem($id, $resolve = 'assets')
    {
        $response = $this->client->get(
            sprintf('contentitems/%s', $id),
            ['resolve' => $resolve],
            $this->userSessionService->getSessionData()
        );
        $json = $response->asJSON();
        $this->verifyResponseStatusCode($json);

        return GetContentItem::parse($json->data);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteContentItem($id)
    {
        $response = $this->client->delete(
            sprintf('contentitems/%s', $id),
            [],
            $this->userSessionService->getSessionData()
        );
        $json = $response->asJSON();
        $this->verifyResponseStatusCode($json);

        return true;
    }

    /**
     * @param int $linkLifetime seconds
     * @param ViewerLinkDto $viewerLinkDto
     *
     * @return string
     */
    public function generateSecureLink($linkLifetime, ViewerLinkDto $viewerLinkDto)
    {
        $response = $this->client->post(
            sprintf('contentItems/generateSecureLinkCmd/?urlttl=%s', $linkLifetime),
            DtoNormalizer::convertToArray($viewerLinkDto),
            $this->userSessionService->getSessionData()
        );
        $json = $response->asJSON();
        $this->verifyResponseStatusCode($json);

        return $json->data->link;
    }

    /**
     * @return AssetType[]
     * @throws BadApiStatusCodeException
     */
    public function getAssetTypes()
    {
        $response = $this->client->get(
            'contentitems/assettypes',
            []
        );
        $json = $response->asJSON();
        $this->verifyResponseStatusCode($json);

        $assetTypes = [];

        foreach ($json->data as $assetType) {
            $assetTypes[] = AssetType::parse($assetType);
        }

        return $assetTypes;
    }

    /**
     * @param ContentGroupDto $contentGroupDto
     * @return ContentGroup
     * @throws BadApiStatusCodeException
     */
    public function createContentGroup(ContentGroupDto $contentGroupDto)
    {
        $response = $this->client->post(
            'contentgroups',
            DtoNormalizer::convertToArray($contentGroupDto),
            $this->userSessionService->getSessionData()
        );
        $json = $response->asJSON();
        $this->verifyResponseStatusCode($json);

        return ContentGroup::parse($json->data);
    }

    /**
     * @param string $contentGroupId
     * @param string[] $contentItems
     */
    public function addContentItemsToContentGroup($contentGroupId, array $contentItems)
    {
        $this->client->post(
            sprintf('contentgroups/%s/contentitems', $contentGroupId),
            $contentItems,
            $this->userSessionService->getSessionData()
        );
    }

    /**
     * @param array $assetIds
     * @return DocumentOperationBegin
     * @throws BadApiStatusCodeException
     */
    public function mergeDocuments($assetIds)
    {
        $response = $this->client->post(
            'documentoperations',
            $assetIds,
            $this->userSessionService->getSessionData()
        );

        $json = $response->asJSON();
        $this->verifyResponseStatusCode($json);

        return DocumentOperationBegin::parse($json->data);
    }

    /**
     * @param int $operationId
     * @return DocumentOperationStatus
     * @throws BadApiStatusCodeException
     */
    public function checkDocumentOperation($operationId)
    {
        $response = $this->client->get(
            sprintf('documentoperations/%s', $operationId),
            [],
            $this->userSessionService->getSessionData()
        );

        $json = $response->asJSON();
        $this->verifyResponseStatusCode($json);

        return DocumentOperationStatus::parse($json->data);
    }
}
