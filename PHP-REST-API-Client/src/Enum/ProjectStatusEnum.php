<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient\Enum;

class ProjectStatusEnum
{
    const NEW      = 'new';
    const FEEDBACK = 'feedback';
    const REJECTED = 'rejected';
    const APPROVED = 'approved';
    const FINISHED = 'finished';
    const ERROR    = 'error';
    const IMPORTED = 'imported';

    /** @var string */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function NEW(): ProjectStatusEnum
    {
        return new self(self::NEW);
    }

    public static function FEEDBACK(): ProjectStatusEnum
    {
        return new self(self::FEEDBACK);
    }

    public static function REJECTED(): ProjectStatusEnum
    {
        return new self(self::REJECTED);
    }

    public static function APPROVED(): ProjectStatusEnum
    {
        return new self(self::APPROVED);
    }

    public static function FINISHED(): ProjectStatusEnum
    {
        return new self(self::FINISHED);
    }

    public static function ERROR(): ProjectStatusEnum
    {
        return new self(self::ERROR);
    }

    public static function IMPORTED(): ProjectStatusEnum
    {
        return new self(self::IMPORTED);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}