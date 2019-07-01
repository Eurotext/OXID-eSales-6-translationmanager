<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Exception;

use Psr\Http\Message\RequestInterface as HttpRequestInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Throwable;

class DeserializationFailedException extends \RuntimeException
{
    /** @var HttpRequestInterface */
    private $httpRequest;

    /** @var HttpResponseInterface */
    private $httpResponse;

    public function __construct(
        HttpRequestInterface $httpRequest,
        HttpResponseInterface $httpResponse,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->httpRequest  = $httpRequest;
        $this->httpResponse = $httpResponse;
    }

    public function getHttpRequest(): HttpRequestInterface
    {
        return $this->httpRequest;
    }

    public function getHttpResponse(): HttpResponseInterface
    {
        return $this->httpResponse;
    }
}
