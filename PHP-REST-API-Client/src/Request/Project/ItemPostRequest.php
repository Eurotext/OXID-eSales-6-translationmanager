<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see       PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Request\Project;

use Eurotext\RestApiClient\Request\Data\Project\ItemData;
use Eurotext\RestApiClient\Request\RequestInterface;

class ItemPostRequest implements RequestInterface
{
    /** @var string */
    private $source;

    /** @var string */
    private $target;

    /** @var string */
    private $textType;

    /** @var string */
    private $systemModule;

    /** @var ItemData */
    private $data;

    /**
     * @var int
     */
    private $projectId;

    public function __construct(
        int $projectId,
        string $source,
        string $target,
        string $textType,
        string $systemModule,
        ItemData $data
    ) {
        $this->source       = $source;
        $this->target       = $target;
        $this->textType     = $textType;
        $this->systemModule = $systemModule;
        $this->data         = $data;
        $this->projectId    = $projectId;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getTextType(): string
    {
        return $this->textType;
    }

    public function getSystemModule(): string
    {
        return $this->systemModule;
    }

    public function getData(): ItemData
    {
        return $this->data;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getHeaders(): array
    {
        return [
            'X-Source'        => $this->convertLocale($this->getSource()),
            'X-Target'        => $this->convertLocale($this->getTarget()),
            'X-TextType'      => $this->getTextType(),
            'X-System-Module' => $this->getSystemModule(),
        ];
    }

    private function convertLocale(string $language): string
    {
        return strtolower(str_replace('_', '-', $language));
    }

}
