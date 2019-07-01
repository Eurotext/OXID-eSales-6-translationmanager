<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Response;

use Psr\Http\Message\RequestInterface as HttpRequestInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

class AbstractResponse implements ResponseInterface
{
    /** @var HttpRequestInterface */
    private $httpRequest;

    /** @var HttpResponseInterface */
    private $httpResponse;

    public function getHttpRequest(): HttpRequestInterface
    {
        return $this->httpRequest;
    }

    public function setHttpRequest(HttpRequestInterface $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

    public function getHttpResponse(): HttpResponseInterface
    {
        return $this->httpResponse;
    }

    public function setHttpResponse(HttpResponseInterface $httpResponse)
    {
        $this->httpResponse = $httpResponse;
    }
}
