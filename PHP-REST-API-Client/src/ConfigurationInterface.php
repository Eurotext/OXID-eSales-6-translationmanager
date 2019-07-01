<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient;

interface ConfigurationInterface
{
    public function getApiKey(): string;

    public function getHost(): string;

    public function getUserAgent(): string;

    public function getPluginName(): string;

    public function getPluginVersion(): string;

    public function getSystemName(): string;

    public function getSystemVersion(): string;

    public function getDebug(): bool;

    public function getDebugFile(): string;
}