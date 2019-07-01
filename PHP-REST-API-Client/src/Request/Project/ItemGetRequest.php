<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Request\Project;

use Eurotext\RestApiClient\Request\RequestInterface;

class ItemGetRequest implements RequestInterface
{
    /**
     * @var int
     */
    private $projectId;

    /**
     * @var int
     */
    private $itemId;

    /**
     * @var int
     */
    private $withOrigin;

    public function __construct(
        int $projectId,
        int $itemId,
        int $withOrigin = 0
    ) {
        $this->projectId  = $projectId;
        $this->itemId     = $itemId;
        $this->withOrigin = $withOrigin;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getWithOrigin(): int
    {
        return $this->withOrigin;
    }

    public function getHeaders(): array
    {
        return [];
    }

}
