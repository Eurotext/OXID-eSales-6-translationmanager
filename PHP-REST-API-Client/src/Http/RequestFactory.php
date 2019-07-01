<?php
declare(strict_types=1);

namespace Eurotext\RestApiClient\Http;

use GuzzleHttp\Psr7\Request;

class RequestFactory
{
    const METHOD_POST  = 'POST';
    const METHOD_GET   = 'GET';
    const METHOD_PATCH = 'PATCH';

    public function createPostRequest(
        string $uri,
        array $headers = [],
        string $body = null,
        string $version = '1.1'
    ): Request {
        return new Request(self::METHOD_POST, $uri, $headers, $body, $version);
    }

    public function createGetRequest(string $uri, array $headers = [], string $version = '1.1')
    {
        return new Request(self::METHOD_GET, $uri, $headers, '', $version);
    }

    public function createPatchRequest(
        string $uri,
        array $headers = [],
        string $body = null,
        string $version = '1.1'
    ): Request {
        return new Request(self::METHOD_PATCH, $uri, $headers, $body, $version);
    }

}
