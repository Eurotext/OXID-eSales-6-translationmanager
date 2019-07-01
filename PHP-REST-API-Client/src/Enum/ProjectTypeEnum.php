<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Enum;

class ProjectTypeEnum
{
    const QUOTE = 'quote';
    const ORDER = 'order';

    /** @var string */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function QUOTE(): ProjectTypeEnum
    {
        return new self(self::QUOTE);
    }

    public static function ORDER(): ProjectTypeEnum
    {
        return new self(self::ORDER);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
