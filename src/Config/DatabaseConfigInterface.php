<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Config;

interface DatabaseConfigInterface
{
    public function getHost(): string;

    public function getPort(): int;

    public function getDatabase(): string;

    public function getUsername(): string;

    public function getPassword(): string;

    public function getCharset(): string;

    public function getCollation(): ?string;

    public function getFetchMode(): string;

    public function getOptions(): array;

    public function getTimezone(): string;
}
