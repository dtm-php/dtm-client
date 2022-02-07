<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Api;

interface ApiInterface
{
    public function generateGid(string $dtmServer): string;

    public function prepare(string $dtmServer, array $body);

    public function submit(string $dtmServer, array $body);

    public function abort(string $dtmServer, array $body);

    public function registerBranch(string $dtmServer, array $body);

    public function query(string $dtmServer, array $body);

    public function queryAll(string $dtmServer, array $body);
}
