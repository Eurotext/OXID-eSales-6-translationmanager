<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Request;

use Eurotext\RestApiClient\Enum\ProjectTypeEnum;
use Eurotext\RestApiClient\Request\Data\ProjectData;

class ProjectPostRequest implements RequestInterface
{
    /** @var string */
    private $name;

    /** @var ProjectTypeEnum */
    private $type;

    /** @var string CallbackUrl */
    private $callback;

    /** @var ProjectData */
    private $data;

    public function __construct($name, ProjectData $projectData, ProjectTypeEnum $type = null, $callback = '')
    {
        $this->name     = $name;
        $this->data     = $projectData;
        $this->callback = $callback;
        $this->type     = $type;

        if ($this->type === null) {
            $this->type = ProjectTypeEnum::QUOTE();
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ProjectTypeEnum
    {
        return $this->type;
    }

    public function getData(): ProjectData
    {
        return $this->data;
    }

    public function getCallback(): string
    {
        return $this->callback;
    }

    public function getHeaders(): array
    {
        return [
            'X-Type'     => (string)$this->getType(),
            'X-Name'     => $this->getName(),
            'X-Callback' => $this->getCallback(),
        ];
    }
}
