<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Api;

use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\RestApiClient\Enum\ProjectTypeEnum;
use Eurotext\RestApiClient\Exception\DeserializationFailedException;
use Eurotext\RestApiClient\Request\Data\ProjectData;
use Eurotext\RestApiClient\Request\ProjectGetRequest;
use Eurotext\RestApiClient\Request\ProjectPostRequest;
use Eurotext\RestApiClient\Request\ProjectTransitionRequest;
use Eurotext\RestApiClient\Request\ProjectTranslateRequest;
use Eurotext\RestApiClient\Response\ProjectTransitionResponse;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectV1ApiTest extends TestCase
{
    /** @var ProjectV1Api */
    private $api;

    /** @var \GuzzleHttp\ClientInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $client;

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->createMock(\GuzzleHttp\ClientInterface::class);

        $this->api = new ProjectV1Api(null, $this->client);
    }

    /**
     * @throws DeserializationFailedException
     * @throws GuzzleException
     */
    public function testItShouldSendProjectPost()
    {
        $projectData = new ProjectData('Unit Test');

        $request = new ProjectPostRequest('', $projectData, ProjectTypeEnum::QUOTE());

        $responseStatus  = 200;
        $responseHeaders = [];
        $responseBody    = file_get_contents(__DIR__ . '/_files/project-post-response.json');

        $httpResponse = new \GuzzleHttp\Psr7\Response($responseStatus, $responseHeaders, $responseBody);

        $this->client->expects($this->once())->method('send')->willReturn($httpResponse);

        $response = $this->api->post($request);

        $this->assertEquals(78, $response->getId());
    }

    /**
     * @throws GuzzleException
     */
    public function testItShouldCaptureExceptionsDuringResponseDeserialization()
    {
        $request = new ProjectPostRequest('', new ProjectData(''));

        $responseStatus  = 200;
        $responseHeaders = [];
        $responseBody    = file_get_contents(__DIR__ . '/_files/project-post-response.json');

        $httpResponse = new \GuzzleHttp\Psr7\Response($responseStatus, $responseHeaders, $responseBody);

        $this->client->expects($this->once())->method('send')->willReturn($httpResponse);

        // SERIALIZER
        /** @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->once())->method('deserialize')->willThrowException(new \Exception());

        $api = new ProjectV1Api(null, $this->client, $serializer);

        $response = null;
        try {
            $response = $api->post($request);
        } catch (DeserializationFailedException $e) {
            // we are catching the exception and asserting various parameters
            $this->assertInstanceOf(RequestInterface::class, $e->getHttpRequest());
            $this->assertInstanceOf(ResponseInterface::class, $e->getHttpResponse());
        }

        $this->assertNull($response);
    }

    /**
     * @throws DeserializationFailedException
     * @throws GuzzleException
     */
    public function testItShouldInitializeEmptyResponseObject()
    {
        $request = new ProjectPostRequest('', new ProjectData(''));

        $responseStatus  = 200;
        $responseHeaders = [];
        $responseBody    = file_get_contents(__DIR__ . '/_files/project-post-response.json');

        $httpResponse = new \GuzzleHttp\Psr7\Response($responseStatus, $responseHeaders, $responseBody);

        $this->client->expects($this->once())->method('send')->willReturn($httpResponse);

        // SERIALIZER
        /** @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->once())->method('deserialize')->willReturn(new \StdClass());

        $api = new ProjectV1Api(null, $this->client, $serializer);

        $api->post($request);
    }

    /**
     * @throws GuzzleException
     */
    public function testItShouldTransitionProject()
    {
        $projectId = 27;

        $request = new ProjectTransitionRequest($projectId, ProjectStatusEnum::NEW());

        $responseStatus  = 204;
        $responseHeaders = [];
        $responseBody    = '';

        $httpResponse = new \GuzzleHttp\Psr7\Response($responseStatus, $responseHeaders, $responseBody);

        $this->client->expects($this->once())->method('send')->willReturn($httpResponse);

        $response = $this->api->transition($request);

        $this->assertInstanceOf(ProjectTransitionResponse::class, $response);

        $this->assertEquals($httpResponse, $response->getHttpResponse());
    }

    public function testItShouldGetProjectDetails()
    {
        $projectId = 27;
		
		$projectRequest = new ProjectGetRequest($projectId);

        $responseStatus  = 200;
        $responseHeaders = [];
        $responseBody    = file_get_contents(__DIR__ . '/_files/project-get-response.json');

        $httpResponse = new \GuzzleHttp\Psr7\Response($responseStatus, $responseHeaders, $responseBody);

        $this->client->expects($this->once())->method('send')->willReturn($httpResponse);

        $response = $this->api->get($projectRequest);

        $this->assertSame('the project description', $response->getDescription());
        $this->assertArrayHasKey(1, $response->getItems());
        $this->assertArrayHasKey(2, $response->getItems());
        $this->assertArrayHasKey(3, $response->getItems());
        $this->assertSame([], $response->getFiles());
    }

    public function testItShouldSendTranslateRequest()
    {
        $projectId = 27;

        $responseStatus  = 200;
        $responseHeaders = [];
        $responseBody    = sprintf('{"id":%d}', $projectId);

        $httpResponse = new \GuzzleHttp\Psr7\Response($responseStatus, $responseHeaders, $responseBody);

        $this->client->expects($this->once())->method('send')->willReturn($httpResponse);

        $response = $this->api->translate(new ProjectTranslateRequest($projectId));

        $this->assertEquals($projectId, $response->getId());
    }

    public function testItShouldThrowExceptionOnDeserializationError()
    {
        $this->expectException(DeserializationFailedException::class);
        $this->expectExceptionMessage('Error during deserialization');

        $brokenResponse = new \GuzzleHttp\Psr7\Response(200, [], '[]');

        $this->client->expects($this->once())->method('send')->willReturn($brokenResponse);

        $this->api->get(new ProjectGetRequest(27));
    }

    public function testItShouldThrowAnExceptionOnRequestError()
    {
        $this->expectException(GuzzleException::class);

        $this->client->expects($this->once())->method('send')->willThrowException(new HttpTestException());

        $this->api->get(new ProjectGetRequest(27));
    }

}

/** @noinspection PhpSuperClassIncompatibleWithInterfaceInspection */

class HttpTestException extends \Exception implements GuzzleException
{

}
