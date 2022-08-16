<?php

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