<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Config;

use Hyperf\Contract\ConfigInterface;

class HyperfDatabaseConfig implements DatabaseConfigInterface
{
    private ConfigInterface $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function getHost(): string
    {
        return $this->getConfig('host');
    }

    public function getPort(): int
    {
        return $this->getConfig('port');
    }

    public function getDatabase(): string
    {
        return $this->getConfig('database');
    }

    public function getUsername(): string
    {
        return $this->getConfig('username');
    }

    public function getPassword(): string
    {
        return $this->getConfig('password');
    }

    public function getCharset(): string
    {
        return $this->getConfig('charset');
    }

    public function getCollation(): ?string
    {
        return $this->getConfig('collation');
    }

    public function getFetchMode(): string
    {
        return $this->getConfig('fetch_mode');
    }

    public function getOptions(): array
    {
        return $this->getConfig('options', []);
    }

    public function getTimezone(): string
    {
        return $this->getConfig('timezone', '');
    }

    private function getConfig(string $key, mixed $default = null)
    {
        return $this->config->get('dtm.database.' . $key, $default);
    }
}
