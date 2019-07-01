<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Api\Project;

use Eurotext\RestApiClient\Configuration;
use Eurotext\RestApiClient\Exception\DeserializationFailedException;
use Eurotext\RestApiClient\Http\RequestFactory;
use Eurotext\RestApiClient\Request\Data\Project\ItemData;
use Eurotext\RestApiClient\Request\Project\ItemPostRequest;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Eurotext\RestApiClient\Api\Project\ItemV1Api
 */
class ItemV1ApiTest extends TestCase
{
    const PROJECT_ID = 71;
    const HOST       = 'http://example.com';

    /** @var ItemV1Api */
    private $itemApi;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ClientInterface */
    private $client;

    /** @var \PHPUnit_Framework_MockObject_MockObject|RequestFactory */
    private $requestFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Configuration */
    private $config;

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->createMock(ClientInterface::class);
        $this->config = $this->createMock(Configuration::class);
        $this->config->method('getHost')->willReturn(self::HOST);
        $this->requestFactory = $this->createMock(RequestFactory::class);

        $this->itemApi = new ItemV1Api($this->config, $this->client, null, $this->requestFactory);
    }

    public function testItShouldSendItemPost()
    {
        $itemRequest = $this->createItemRequest();

        $responseStatus  = 200;
        $responseHeaders = [];
        $responseBody    = file_get_contents(__DIR__ . '/../_files/project-item-post-response.json');

        $httpResponse = new \GuzzleHttp\Psr7\Response($responseStatus, $responseHeaders, $responseBody);

        $this->client->expects($this->once())->method('send')->willReturn($httpResponse);

        $response = $this->itemApi->post($itemRequest);

        $this->assertEquals(33, $response->getId());
    }

    public function testUseApiUrlWithProjectId()
    {
        $itemRequest = $this->createItemRequest();

        $responseStatus  = 200;
        $responseHeaders = [];
        $responseBody    = file_get_contents(__DIR__ . '/../_files/project-item-post-response.json');

        $httpResponse = new \GuzzleHttp\Psr7\Response($responseStatus, $responseHeaders, $responseBody);

        $this->client->expects($this->once())->method('send')->willReturn($httpResponse);

        $this->requestFactory
            ->method('createPostRequest')
            ->with('http://example.com/api/v1/project/' . self::PROJECT_ID . '/item.json');

        $this->itemApi->post($itemRequest);
    }

    public function testItShouldThrowExceptionOnHttpException()
    {
        $this->expectException(GuzzleException::class);

        $itemRequest = $this->createItemRequest();

        /** @var GuzzleException|\Exception|\PHPUnit_Framework_MockObject_MockObject $exception */
        $exception = new HttpTestException();
        $this->client->expects($this->once())->method('send')->willThrowException($exception);

        $this->itemApi->post($itemRequest);
    }

    public function testShouldThrowExceptionIfDeserializationFails()
    {
        $this->expectException(DeserializationFailedException::class);
        $this->expectExceptionMessage('Error during deserialization');

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willThrowException(new \Exception('Something went wrong'));
        $this->client->method('send')->willReturn($responseMock);

        $this->itemApi->post($this->createItemRequest());
    }

    /**
     * @return ItemPostRequest
     */
    private function createItemRequest(): ItemPostRequest
    {
        $source       = 'en_US';
        $target       = 'de_DE';
        $textType     = 'my_type';
        $systemModule = 'Magento';

        $itemData = new ItemData(
            [
                'description' => 'Please translate me!',
            ],
            [
                'item_id'   => 27,
                'more_meta' => 'eurotext are the best',
            ]
        );

        $itemRequest = new ItemPostRequest(self::PROJECT_ID, $source, $target, $textType, $systemModule, $itemData);

        return $itemRequest;
    }
}

/** @noinspection PhpSuperClassIncompatibleWithInterfaceInspection */

class HttpTestException extends \Exception implements GuzzleException
{

}
