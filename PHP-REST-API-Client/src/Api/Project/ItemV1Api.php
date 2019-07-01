<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Api\Project;

use Eurotext\RestApiClient\Api\AbstractV1Api;
use Eurotext\RestApiClient\Request\Project\ItemGetRequest;
use Eurotext\RestApiClient\Request\Project\ItemPostRequest;
use Eurotext\RestApiClient\Response\Project\ItemGetResponse;
use Eurotext\RestApiClient\Response\Project\ItemPostResponse;

class ItemV1Api extends AbstractV1Api implements ItemV1ApiInterface
{
    const API_URL = '/api/v1/project/{project_id}/item.json';

    const REQUEST_CONTENT_TYPE = 'application/json';

    /**
     * @param ItemPostRequest $request
     *
     * @return ItemPostResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(ItemPostRequest $request): ItemPostResponse
    {
        $projectId = $request->getProjectId();

        $httpPath    = "/api/v1/project/$projectId/item.json";
        $httpBody    = $this->serializer->serialize($request->getData(), 'json');
        $httpHeaders = $request->getHeaders();

        $response = $this->sendRequestAndHandleResponse(
            $this->createHttpPostRequest($httpPath, $httpHeaders, $httpBody),
            $this->createHttpClientOptions(),
            ItemPostResponse::class
        );

        /** @var ItemPostResponse $response */
        return $response;
    }

    /**
     * @param ItemGetRequest $request
     *
     * @return ItemGetResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(ItemGetRequest $request): ItemGetResponse
    {
        $projectId  = $request->getProjectId();
        $itemId     = $request->getItemId();
        $withOrigin = $request->getWithOrigin();

        $httpPath = "/api/v1/project/$projectId/item/$itemId.json";
        if ($withOrigin === 1) {
            $httpPath .= '?withOrigin=1';
        }
        $httpHeaders = $request->getHeaders();

        $response = $this->sendRequestAndHandleResponse(
            $this->createHttpGetRequest($httpPath, $httpHeaders),
            $this->createHttpClientOptions(),
            ItemGetResponse::class
        );

        /** @var ItemGetResponse $response */
        return $response;
    }

}
