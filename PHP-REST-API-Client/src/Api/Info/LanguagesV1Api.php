<?php
declare(strict_types=1);

namespace Eurotext\RestApiClient\Api\Info;

use Eurotext\RestApiClient\Configuration;
use Eurotext\RestApiClient\ConfigurationInterface;
use Eurotext\RestApiClient\Exception\DeserializationFailedException;
use Eurotext\RestApiClient\Exception\InfoApiException;
use Eurotext\RestApiClient\Http\RequestFactory;
use Eurotext\RestApiClient\Response\Info\LanguagesGetResponse;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;

class LanguagesV1Api
{
    const REQUEST_CONTENT_TYPE = 'application/json';

    public function __construct(
        ConfigurationInterface $config = null,
        ClientInterface $client = null,
        RequestFactory $requestFactory = null
    ) {
        $this->config         = $config ?: new Configuration();
        $this->client         = $client ?: new Client();
        $this->requestFactory = $requestFactory ?: new RequestFactory();
    }

    public function get()
    {
        $httpRequest = $this->createHttpRequest(RequestFactory::METHOD_GET, '/api/v1/info/languages.json');

        $httpOptions = $this->createHttpClientOption();

        // Send Request
        try {
            $httpResponse = $this->client->send($httpRequest, $httpOptions);
        } catch (GuzzleException $e) {
            throw new InfoApiException($httpRequest, 'Error during the request', 0, $e);
        }

        // Handle Response: Deserzialize Response JSON to PHP Object
        try {
            $responseContent = $httpResponse->getBody()->getContents();

            /** @var LanguagesGetResponse $response */
            $response = new LanguagesGetResponse(json_decode($responseContent, true));
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Error during json_decode: ' . json_last_error_msg());
            }
        } catch (\Exception $e) {
            $failedException = new DeserializationFailedException(
                $httpRequest, $httpResponse, 'Error during deserialization', 0, $e
            );

            throw $failedException;
        }

        $response->setHttpRequest($httpRequest);
        $response->setHttpResponse($httpResponse);

        return $response;
    }

    /**
     * @todo refactor into RequestFactory
     */
    private function createHttpRequest(string $method, string $path, string $httpBody = null): Request
    {
        $uri = $this->config->getHost() . $path;

        $headers                 = [];
        $headers['Content-Type'] = self::REQUEST_CONTENT_TYPE;
        $headers['User-Agent']   = $this->config->getUserAgent();
        $headers['apikey']       = $this->config->getApiKey();

        return $this->requestFactory->createGetRequest($uri, $headers);
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    private function createHttpClientOption(): array
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'ab');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }

}
