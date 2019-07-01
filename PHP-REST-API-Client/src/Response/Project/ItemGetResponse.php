<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Response\Project;

use Eurotext\RestApiClient\Request\Data\Project\ItemData;
use Eurotext\RestApiClient\Response\AbstractResponse;

class ItemGetResponse extends AbstractResponse
{
    /** @var ItemData */
    private $itemData;

    /** @var ItemData */
    private $originItemData;

    public function getItemStatus()
    {
        $xItemStatus = $this->getHttpResponse()->getHeader('X-Item-Status');
        $status      = array_shift($xItemStatus);

        return $status;
    }

    public function setData(array $data)
    {
        $this->itemData = $this->initItemData($data);
    }

    public function setOrigin(array $origin)
    {
        $this->originItemData = $this->initItemData($origin);
    }

    public function getItemData(): ItemData
    {
        return $this->itemData;
    }

    public function getOriginItemData(): ItemData
    {
        return $this->originItemData;
    }

    private function initItemData(array $data): ItemData
    {
        $meta = $data['__meta'];
        unset($data['__meta']);

        return new ItemData($data, $meta);
    }

}
