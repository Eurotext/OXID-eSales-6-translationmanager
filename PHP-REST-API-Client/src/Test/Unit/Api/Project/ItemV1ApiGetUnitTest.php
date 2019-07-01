<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Api\Project;

use Eurotext\RestApiClient\Configuration;
use Eurotext\RestApiClient\Http\RequestFactory;
use Eurotext\RestApiClient\Request\Project\ItemGetRequest;
use Eurotext\RestApiClient\Response\Project\ItemGetResponse;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Eurotext\RestApiClient\Api\Project\ItemV1Api
 */
class ItemV1ApiGetUnitTest extends TestCase
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

    const ITEM_ID = 1234;

    const BASE_URL = 'https://sandbox.api.eurotext.de/api/v1/project/%d/item/%d.json';

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->createMock(ClientInterface::class);

        $this->requestFactory = $this->createMock(RequestFactory::class);

        $this->config = new Configuration();

        $this->itemApi = new ItemV1Api($this->config, $this->client, null, $this->requestFactory);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testItShouldSendItemGet()
    {
        $url = sprintf(self::BASE_URL, self::PROJECT_ID, self::ITEM_ID);
        $this->mockRequestFactory($url);

        $this->client->expects($this->once())->method('send')->willReturn($this->newHttpResponse());

        $itemRequest = new ItemGetRequest(self::PROJECT_ID, self::ITEM_ID, 0);

        $response = $this->itemApi->get($itemRequest);

        $this->assertItemGetResponse($response);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testItShouldSendItemGetWithOriginData()
    {
        $url = sprintf(self::BASE_URL . '?withOrigin=1', self::PROJECT_ID, self::ITEM_ID);
        $this->mockRequestFactory($url);

        $this->client->expects($this->once())->method('send')->willReturn($this->newHttpResponse());

        $itemRequest = new ItemGetRequest(self::PROJECT_ID, self::ITEM_ID, 1);

        $response = $this->itemApi->get($itemRequest);

        $this->assertItemGetResponse($response);
    }

    private function newHttpResponse(): \GuzzleHttp\Psr7\Response
    {
        $responseBody = file_get_contents(__DIR__ . '/../_files/project-item-get-response.json');

        return new \GuzzleHttp\Psr7\Response(200, [], $responseBody);
    }

    /**
     * @param $response
     */
    private function assertItemGetResponse(ItemGetResponse $response)
    {
        $this->assertInstanceOf(RequestInterface::class, $response->getHttpRequest());
        $this->assertInstanceOf(ResponseInterface::class, $response->getHttpResponse());

        $itemData = $response->getItemData();
        $this->assertEquals('kcapkcaB timmuS nworC', $itemData->getDataValue('name'));

        $originItemData = $response->getOriginItemData();
        $this->assertEquals('Crown Summit Backpack', $originItemData->getDataValue('name'));
    }

    private function mockRequestFactory($url)
    {
        $request = (new RequestFactory())->createGetRequest($url);
        $this->requestFactory->expects($this->once())->method('createGetRequest')
                             ->with($url)->willReturn($request);
    }
}
