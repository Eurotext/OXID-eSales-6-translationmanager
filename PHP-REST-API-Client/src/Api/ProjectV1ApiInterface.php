<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Api;

use Eurotext\RestApiClient\Exception\DeserializationFailedException;
use Eurotext\RestApiClient\Request\ProjectGetRequest;
use Eurotext\RestApiClient\Request\ProjectPostRequest;
use Eurotext\RestApiClient\Request\ProjectTransitionRequest;
use Eurotext\RestApiClient\Request\ProjectTranslateRequest;
use Eurotext\RestApiClient\Response\ProjectGetResponse;
use Eurotext\RestApiClient\Response\ProjectPostResponse;
use Eurotext\RestApiClient\Response\ProjectTransitionResponse;
use Eurotext\RestApiClient\Response\ProjectTranslateResponse;

interface ProjectV1ApiInterface
{
    /**
     * @param ProjectPostRequest $request
     *
     * @return ProjectPostResponse
     * @throws DeserializationFailedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(ProjectPostRequest $request): ProjectPostResponse;

    /**
     * @param ProjectTransitionRequest $request
     *
     * @return ProjectTransitionResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function transition(ProjectTransitionRequest $request): ProjectTransitionResponse;

    /**
     * @param ProjectGetRequest $request
     *
     * @return ProjectGetResponse
     */
    public function get(ProjectGetRequest $request): ProjectGetResponse;

    /**
     * @deprecated ONLY AVAILABLE IN SANDBOX, to simulate translated project
     *
     * @param ProjectTranslateRequest $request
     *
     * @return ProjectTranslateResponse
     */
    public function translate(ProjectTranslateRequest $request): ProjectTranslateResponse;
}