<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Validator;

use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;

/**
 * Check that every item has the defined ProjectStatus
 * If one of the items has a different Status the service will return false
 *
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */
interface ProjectStatusValidatorInterface
{
    public function validate(ProjectInterface $project, ProjectStatusEnum $projectStatus);
}