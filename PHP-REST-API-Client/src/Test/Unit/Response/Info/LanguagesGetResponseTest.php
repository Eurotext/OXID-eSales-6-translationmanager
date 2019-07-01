<?php
declare(strict_types=1);

namespace Eurotext\RestApiClient\Response\Info;

use Eurotext\RestApiClient\Response\AbstractResponse;
use PHPUnit\Framework\TestCase;

class LanguagesGetResponseTest extends TestCase
{
    const LANGUAGES = ['de-de' => 'German (Germany)', 'en-us' => 'English (United States)'];

    /** @var LanguagesGetResponse */
    private $response;

    protected function setUp()
    {
        $languages      = self::LANGUAGES;
        $this->response = new LanguagesGetResponse($languages);
    }

    public function testExtendsAbstractResponse()
    {
        $this->assertInstanceOf(AbstractResponse::class, $this->response);
    }

    public function testGetLanguages()
    {
        $this->assertSame(self::LANGUAGES, $this->response->getLanguages());
    }
}
