<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Grpc;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class GrpcClientManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $manager = new GrpcClientManager();
        $config = $container->get(ConfigInterface::class);
        $server = $config->get('dtm.server', '127.0.0.1');
        $port = $config->get('dtm.port.grpc', 36790);
        $hostname = $server . ':' . $port;
        $manager->addClient($hostname, new GrpcClient($hostname));
        return $manager;
    }
}
