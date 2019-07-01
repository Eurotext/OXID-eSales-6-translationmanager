<?php
declare(strict_types=1);

namespace Eurotext\RestApiClient\Api\Info;

use Eurotext\RestApiClient\Response\Info\LanguagesGetResponse;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class LanguagesV1ApiTest extends TestCase
{
    /** @var LanguagesV1Api */
    private $api;

    /** @var Client|\PHPUnit_Framework_MockObject_MockObject */
    private $client;

    protected function setUp()
    {
        $this->client = $this->createMock(\GuzzleHttp\ClientInterface::class);

        $this->api = new LanguagesV1Api(null, $this->client);
    }

    public function testGetReturnsResponseWithLanguages()
    {
        $responseStatus  = 200;
        $responseHeaders = [];
        $responseBody    = file_get_contents(__DIR__ . '/../_files/info-languages-get-response.json');

        $httpResponse = new \GuzzleHttp\Psr7\Response($responseStatus, $responseHeaders, $responseBody);

        $this->client->expects($this->once())->method('send')->willReturn($httpResponse);

        $response = $this->api->get();

        $this->assertInstanceOf(LanguagesGetResponse::class, $response);
        $this->assertArrayHasKey('ar-ae', $response->getLanguages());
        $this->assertCount(6, $response->getLanguages());
    }

    public function _testThrowsExceptionOnDeserializeError()
    {
        // @todo implement
    }

    public function _testThrowsExceptionOnRequestError()
    {
        // @todo implement
    }
}
