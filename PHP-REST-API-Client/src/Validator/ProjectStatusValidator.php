<?php
declare(strict_types=1);

namespace Eurotext\RestApiClient\Validator;

use Eurotext\RestApiClient\Api\ProjectV1ApiInterface;
use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\RestApiClient\Request\ProjectGetRequest;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;

/**
 * Check that every item has the defined ProjectStatus
 * If one of the items has a different Status the service will return false
 *
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */
class ProjectStatusValidator implements ProjectStatusValidatorInterface
{

    /**
     * @var ProjectV1ApiInterface
     */
    private $projectV1Api;

    public function __construct(ProjectV1ApiInterface $projectV1Api)
    {
        $this->projectV1Api = $projectV1Api;
    }

    public function validate(ProjectInterface $project, ProjectStatusEnum $projectStatus)
    {
        $projectId      = $project->getExtId();
        $expectedStatus = (string)$projectStatus;

        $request = new ProjectGetRequest($projectId);

        $response = $this->projectV1Api->get($request);

        $items = $response->getItems();

        foreach ($items as $key => $item) {
            $itemStatus = $item->getStatus();

            if ($itemStatus !== $expectedStatus) {
                return false;
            }
        }

        return true;
    }
}