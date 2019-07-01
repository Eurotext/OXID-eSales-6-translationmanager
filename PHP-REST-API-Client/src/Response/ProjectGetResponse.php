<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Response;

use Eurotext\RestApiClient\Request\Data\Project\ItemData;

class ProjectGetResponse extends AbstractResponse
{
    /** @var string */
    private $description;

    /** @var ItemData[] */
    private $items;

    /** @var mixed[] */
    private $files;

    /**
     * @param string $description
     * @param ItemData[] $items
     * @param mixed[] $files
     */
    public function __construct(string $description, array $items, array $files = [])
    {
        $this->initItems($items);
        $this->description = $description;
        $this->files       = $files;
    }

    private function initItems(array $items)
    {
        foreach ($items as $key => $itemData) {
            if ($itemData instanceof ItemData) {
                $this->items[$key] = $itemData;
                continue;
            }

            $meta = [];
            if (array_key_exists('__meta', $itemData)) {
                $meta = $itemData['__meta'];
                unset($itemData['__meta']);
            }

            $this->items[$key] = new ItemData($itemData, $meta);
        }
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return ItemData[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return mixed[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
