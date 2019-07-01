<?php
declare(strict_types=1);

namespace Eurotext\RestApiClient\Http;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class RequestFactoryTest extends TestCase
{
    /** @var RequestFactory */
    private $factory;

    protected function setUp()
    {
        $this->factory = new RequestFactory();
    }

    public function testCreatePostRequestReturnsRequest()
    {
        $this->assertInstanceOf(Request::class, $this->factory->createPostRequest('http://example.com'));
    }

    public function testCreatePostRequestUseMethodPost()
    {
        $this->assertEquals(
            RequestFactory::METHOD_POST, $this->factory->createPostRequest('http://example.com')->getMethod()
        );
    }

    public function testCreateGetRequestReturnsRequest()
    {
        $this->assertInstanceOf(Request::class, $this->factory->createGetRequest('http://example.com'));
    }

    public function testCreateGetRequestUseMethodGet()
    {
        $this->assertEquals(
            RequestFactory::METHOD_GET, $this->factory->createGetRequest('http://example.com')->getMethod()
        );
    }
}
