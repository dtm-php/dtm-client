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
namespace DtmPhp\DtmClient;

use DtmPhp\DtmClient\Api\ApiInterface;
use Psr\Container\ContainerInterface;

class TCC
{
    protected ContainerInterface $container;

    protected ApiInterface $api;

    protected array $branch = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $apiFactory = $this->container->get(ApiFactory::class);
        $this->api = $apiFactory->create();
    }

    public function generateGid(string $dtmService): string
    {
        return $this->api->generateGid($dtmService);
    }

    public function tccGlobalTransaction(string $dtmServer, string $gid, callable $callback)
    {
    }

    public function callBranch(array $body, string $tryUrl, string $confirmUrl, string $cancelUrl)
    {

    }
}
