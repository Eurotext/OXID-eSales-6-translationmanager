<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Request;

use Eurotext\RestApiClient\Enum\ProjectStatusEnum;

class ProjectTransitionRequest implements RequestInterface
{
    /**
     * @var int
     */
    private $projectId;

    /**
     * @var ProjectStatusEnum
     */
    private $status;

    public function __construct(int $projectId, ProjectStatusEnum $status)
    {
        $this->projectId = $projectId;
        $this->status    = $status;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getStatus(): ProjectStatusEnum
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return [
            'X-Item-Status' => (string)$this->getStatus(),
        ];
    }
}