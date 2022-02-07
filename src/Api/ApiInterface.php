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
namespace DtmClient\Api;

interface ApiInterface
{
    public function generateGid(): string;

    public function prepare(array $body);

    public function submit(array $body);

    public function abort(array $body);

    public function registerBranch(array $body);

    public function query(array $body);

    public function queryAll(array $body);
}
