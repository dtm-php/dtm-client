<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Api;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;
use Psr\Container\ContainerInterface;

class HttpApiFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $server = $config->get('dtm-client.server');
        $port = $config->get('dtm-client.port');
        $clientFactory = $container->get(ClientFactory::class);
        return $clientFactory->create([
            'base_uri' => $server . ':' . $port,
        ]);
    }
}
