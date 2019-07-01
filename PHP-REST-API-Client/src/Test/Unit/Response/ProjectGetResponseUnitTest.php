<?php
declare(strict_types=1);

use Eurotext\RestApiClient\Request\Data\Project\ItemData;
use Eurotext\RestApiClient\Response\ProjectGetResponse;
use PHPUnit\Framework\TestCase;

/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */
class ProjectGetResponseUnitTest extends TestCase
{
    public function testItShouldConvertItemArrayToItemData()
    {
        $status      = 'new';
        $description = 'DESCRIPTION WITH SOME CONTENT';

        $metdaData = [
            'item_id'   => 27,
            'more_meta' => 'eurotext are the best',
        ];

        $items    = [];
        $items[0] = [
            'description' => $description,
            '__meta'      => $metdaData,
            'status'      => $status,
        ];

        $sut = new ProjectGetResponse($description, $items);

        $itemsResult = $sut->getItems();
        $this->assertArrayHasKey(0, $itemsResult);

        /** @var ItemData $item */
        $item = $itemsResult[0];
        $this->assertEquals($description, $item->getDataValue('description'));
        $this->assertEquals($status, $item->getStatus());
        $this->assertSame($metdaData, $item->getMeta());

    }
}