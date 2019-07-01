<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Enum;

class ProjectItemStatusEnum
{
    const NEW         = 'new';
    const IMPORTED    = 'imported';
    const IN_PROGRESS = 'in_progress';
    const FINISHED    = 'finished';
    const REJECTED    = 'rejected';

    /** @var string */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function IMPORTED(): ProjectItemStatusEnum
    {
        return new self(self::IMPORTED);
    }

    public static function NEW(): ProjectItemStatusEnum
    {
        return new self(self::NEW);
    }

    public static function FINISHED(): ProjectItemStatusEnum
    {
        return new self(self::FINISHED);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}