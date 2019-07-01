<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Api;

use Eurotext\RestApiClient\Request\ProjectGetRequest;
use Eurotext\RestApiClient\Request\ProjectPostRequest;
use Eurotext\RestApiClient\Request\ProjectTransitionRequest;
use Eurotext\RestApiClient\Request\ProjectTranslateRequest;
use Eurotext\RestApiClient\Response\ProjectGetResponse;
use Eurotext\RestApiClient\Response\ProjectPostResponse;
use Eurotext\RestApiClient\Response\ProjectTransitionResponse;
use Eurotext\RestApiClient\Response\ProjectTranslateResponse;

class ProjectV1Api extends AbstractV1Api implements ProjectV1ApiInterface
{
    public function post(ProjectPostRequest $request): ProjectPostResponse
    {
        $httpPath    = '/api/v1/project.json';
        $httpHeaders = $request->getHeaders();
        $httpBody    = $this->serializer->serialize($request->getData(), 'json');

        $response = $this->sendRequestAndHandleResponse(
            $this->createHttpPostRequest($httpPath, $httpHeaders, $httpBody),
            $this->createHttpClientOptions(),
            ProjectPostResponse::class
        );

        /** @var ProjectPostResponse $response */
        return $response;
    }

    /**
     * @param ProjectGetRequest $request
     *
     * @return ProjectGetResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(ProjectGetRequest $request): ProjectGetResponse
    {
    	$projectId = $request->getProjectId();
    
        $httpPath = "/api/v1/project/$projectId.json";

        $response = $this->sendRequestAndHandleResponse(
            $this->createHttpGetRequest($httpPath),
            $this->createHttpClientOptions(),
            ProjectGetResponse::class
        );

        /** @var ProjectGetResponse $response */
        return $response;
    }

    /**
     * @param ProjectTransitionRequest $request
     *
     * @return ProjectTransitionResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function transition(ProjectTransitionRequest $request): ProjectTransitionResponse
    {
        $projectId = $request->getProjectId();
        $httpPath  = "/api/v1/transition/project/$projectId.json";

        $httpHeaders = $request->getHeaders();

        $response = $this->sendRequestAndHandleResponse(
            $this->createHttpPatchRequest($httpPath, $httpHeaders),
            $this->createHttpClientOptions(),
            ProjectTransitionResponse::class
        );

        /** @var ProjectTransitionResponse $response */
        return $response;
    }

    /**
     * @param ProjectTranslateRequest $request
     *
     * @return ProjectTranslateResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @deprecated ONLY AVAILABLE IN SANDBOX, to simulate translated project
     */
    public function translate(ProjectTranslateRequest $request): ProjectTranslateResponse
    {
        $projectId = $request->getProjectId();
        $httpPath  = "/api/v1/project/translate/$projectId.json";

        $httpHeaders = $request->getHeaders();

        $response = $this->sendRequestAndHandleResponse(
            $this->createHttpPatchRequest($httpPath, $httpHeaders),
            $this->createHttpClientOptions(),
            ProjectTranslateResponse::class
        );

        /** @var ProjectTranslateResponse $response */
        return $response;
    }
}
