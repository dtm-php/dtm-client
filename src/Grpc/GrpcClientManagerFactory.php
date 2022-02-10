<?php

namespace DtmClient\Grpc;


use DtmClient\TransContext;
use Hyperf\Contract\ConfigInterface;
use PDO;
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