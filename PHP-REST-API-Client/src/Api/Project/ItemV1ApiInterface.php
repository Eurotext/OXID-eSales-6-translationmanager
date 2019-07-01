<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Api\Project;

use Eurotext\RestApiClient\Request\Project\ItemPostRequest;
use Eurotext\RestApiClient\Request\Project\ItemGetRequest;
use Eurotext\RestApiClient\Response\Project\ItemGetResponse;
use Eurotext\RestApiClient\Response\Project\ItemPostResponse;

interface ItemV1ApiInterface
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(ItemPostRequest $request): ItemPostResponse;

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(ItemGetRequest $request): ItemGetResponse;
}