<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Response;

use Psr\Http\Message\RequestInterface as HttpRequestInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

interface ResponseInterface
{
    public function getHttpRequest(): HttpRequestInterface;

    public function setHttpRequest(HttpRequestInterface $httpRequest);

    public function getHttpResponse(): HttpResponseInterface;

    public function setHttpResponse(HttpResponseInterface $httpResponse);
}