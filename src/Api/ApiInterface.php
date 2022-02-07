<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Dtm\DtmClient\Api;

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
