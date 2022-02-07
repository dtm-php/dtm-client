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
return [
    'protocol' => \DtmClient\Constants\Protocol::HTTP,
    'server' => '127.0.0.1',
    'port' => [
        'http' => 36789,
        'grpc' => 36790,
    ],
];
