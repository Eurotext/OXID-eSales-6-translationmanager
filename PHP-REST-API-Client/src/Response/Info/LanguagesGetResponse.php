<?php
declare(strict_types=1);

namespace Eurotext\RestApiClient\Response\Info;

use Eurotext\RestApiClient\Response\AbstractResponse;

class LanguagesGetResponse extends AbstractResponse
{
    /**
     * @var string[]
     */
    private $languages;

    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }

    public function getLanguages()
    {
        return $this->languages;
    }
}
