<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Api;

use Eurotext\RestApiClient\Configuration;
use Eurotext\RestApiClient\ConfigurationInterface;
use Eurotext\RestApiClient\Exception\DeserializationFailedException;
use Eurotext\RestApiClient\Http\RequestFactory;
use Eurotext\RestApiClient\Response\ResponseInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractV1Api
{
    /** @var ClientInterface */
    protected $client;

    /** @var ConfigurationInterface */
    protected $config;

    /** @var SerializerInterface */
    protected $serializer;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    public function __construct(
        ConfigurationInterface $config = null,
        ClientInterface $client = null,
        SerializerInterface $serializer = null,
        RequestFactory $requestFactory = null
    ) {
        $this->config         = $config ?: new Configuration();
        $this->client         = $client ?: new Client();
        $this->serializer     = $serializer ?: $this->constructSerializer();
        $this->requestFactory = $requestFactory ?: new RequestFactory();
    }

    /**
     * @param RequestInterface $httpRequest
     * @param array $httpOptions
     * @param string $responseClass
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequestAndHandleResponse(
        RequestInterface $httpRequest,
        array $httpOptions,
        string $responseClass
    ): ResponseInterface {
        // Send Request
        $httpResponse = $this->client->send($httpRequest, $httpOptions);

        // Handle Response: Deserzialize Response JSON to PHP Object
        $response = null;
        try {
            $responseContent = $httpResponse->getBody()->getContents();

            if (!empty($responseContent)) {
                $response = $this->serializer->deserialize($responseContent, $responseClass, 'json');
            }
        } catch (\Exception $e) {
            throw new DeserializationFailedException(
                $httpRequest,
                $httpResponse,
                'Error during deserialization',
                0,
                $e
            );
        }

        if (!$response instanceof $responseClass) {
            $response = new $responseClass();
        }

        $response->setHttpRequest($httpRequest);
        $response->setHttpResponse($httpResponse);

        /** @var ResponseInterface $response */
        return $response;
    }

    protected function createHttpPostRequest(string $path, array $headers = [], string $httpBody = null): Request
    {
        $uri = rtrim($this->config->getHost(), '/') . $path;

        $httpHeaders = [
            'Content-Type' => 'application/json',
            'User-Agent'   => $this->config->getUserAgent(),
            'apikey'       => $this->config->getApiKey(),
        ];

        if (!empty($headers)) {
            $httpHeaders = array_merge($httpHeaders, $headers);
        }

        return $this->requestFactory->createPostRequest($uri, $httpHeaders, $httpBody);
    }

    protected function createHttpGetRequest(string $path, array $headers = []): Request
    {
        $uri = $this->config->getHost() . $path;

        $httpHeaders = [
            'User-Agent' => $this->config->getUserAgent(),
            'apikey'     => $this->config->getApiKey(),
        ];

        if (!empty($headers)) {
            $httpHeaders = array_merge($httpHeaders, $headers);
        }

        return $this->requestFactory->createGetRequest($uri, $httpHeaders);
    }

    protected function createHttpPatchRequest(string $path, array $headers = [], string $httpBody = null): Request
    {
        $uri = $this->config->getHost() . $path;

        $httpHeaders = [
            'Content-Type' => 'application/json',
            'User-Agent'   => $this->config->getUserAgent(),
            'apikey'       => $this->config->getApiKey(),
        ];

        if (!empty($headers)) {
            $httpHeaders = array_merge($httpHeaders, $headers);
        }

        return $this->requestFactory->createPatchRequest($uri, $httpHeaders, $httpBody);
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOptions(): array
    {
        $options = [];

        $options['headers'] = [
            'X-System'         => $this->config->getSystemName(),
            'X-System-Version' => $this->config->getSystemVersion(),
            'X-Plugin'         => $this->config->getPluginName(),
            'X-Plugin-Version' => $this->config->getPluginVersion(),
        ];

        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'ab');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }

    protected function constructSerializer(): SerializerInterface
    {
        $encoders    = [
            new JsonEncoder(),
        ];
        $normalizers = [
            new JsonSerializableNormalizer(),
            new ObjectNormalizer(null, null, null, new ReflectionExtractor()),
            new ArrayDenormalizer(),
        ];

        return new Serializer(
            $normalizers,
            $encoders
        );
    }

}
