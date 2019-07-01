<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\RestApiClient;

class Configuration implements ConfigurationInterface
{
    const USER_AGENT = 'Eurotext/REST-Api/PHP-Client';
    const PLUGIN_NAME = 'eurotext/lib.eurotext.rest-api-client';
    const PLUGIN_VERSION = '1.0.0';

    /** @var string */
    private $apiKey = '';

    /** @var string */
    private $host = 'https://sandbox.api.eurotext.de';

    /** @var string */
    private $userAgent = self::USER_AGENT;

    /** @var string */
    private $pluginName = self::PLUGIN_NAME;

    /** @var string */
    private $pluginVersion = self::PLUGIN_VERSION;

    /** @var string */
    private $systemName = '';

    /** @var string */
    private $systemVersion = '';

    /** @var bool */
    private $debug = false;

    /** @var string */
    private $debugFile = 'php://output';

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setHost(string $host)
    {
        $this->host = $host;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setUserAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function setPluginName(string $pluginName)
    {
        $this->pluginName = $pluginName;
    }

    public function getPluginVersion(): string
    {
        return $this->pluginVersion;
    }

    public function setPluginVersion(string $pluginVersion)
    {
        $this->pluginVersion = $pluginVersion;
    }

    public function getSystemName(): string
    {
        return $this->systemName;
    }

    public function setSystemName(string $systemName)
    {
        $this->systemName = $systemName;
    }

    public function getSystemVersion(): string
    {
        return $this->systemVersion;
    }

    public function setSystemVersion(string $systemVersion)
    {
        $this->systemVersion = $systemVersion;
    }

    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    public function getDebug(): bool
    {
        return $this->debug;
    }

    public function setDebugFile(string $debugFile)
    {
        $this->debugFile = $debugFile;
    }

    public function getDebugFile(): string
    {
        return $this->debugFile;
    }
}
