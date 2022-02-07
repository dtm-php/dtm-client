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
namespace DtmClient;

use DtmClient\Api\ApiInterface;
use DtmClient\Api\HttpApi;
use DtmClient\Constants\Protocol;
use DtmClient\Exception\UnsupportedException;
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
