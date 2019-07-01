<?php
declare(strict_types=1);

namespace Eurotext\RestApiClient\Request\Converter;

use PHPUnit\Framework\TestCase;

class ItemAttributeTest extends TestCase
{
    /** @var ItemAttribute */
    private $converter;

    protected function setUp()
    {
        $this->converter = new ItemAttribute();
    }

    public function testItShouldNormalize()
    {
        $this->assertSame($this->converter->normalize('meta'), '__meta');
        $this->assertSame($this->converter->normalize('originalString'), 'original_string');
        $this->assertSame($this->converter->normalize('id'), 'id');
        $this->assertSame($this->converter->normalize('camelCase'), 'camelCase');
    }

    public function testItShouldDenormalize()
    {
        $this->assertSame($this->converter->denormalize('__meta'), 'meta');
        $this->assertSame($this->converter->denormalize('original_string'), 'originalString');
        $this->assertSame($this->converter->denormalize('id'), 'id');
        $this->assertSame($this->converter->denormalize('camelCase'), 'camelCase');
    }
}
