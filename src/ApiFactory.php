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
namespace Dtm\DtmClient;

use Dtm\DtmClient\Api\ApiInterface;
use Dtm\DtmClient\Api\HttpApi;
use Dtm\DtmClient\Constants\Protocol;
use Dtm\DtmClient\Exception\UnsupportedException;
use Psr\Container\ContainerInterface;

class ApiFactory
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create(): ApiInterface
    {
        $protocol = config('dtm-client.protocol');
        switch ($protocol) {
            case Protocol::HTTP:
                return $this->container->get(HttpApi::class);
            default:
                throw new UnsupportedException();
        }
    }
}
