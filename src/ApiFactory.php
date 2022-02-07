<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Api\ApiInterface;
use DtmClient\Api\GrpcApi;
use DtmClient\Api\HttpApi;
use DtmClient\Constants\Protocol;
use DtmClient\Exception\UnsupportedException;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class ApiFactory
{

    public function __invoke(ContainerInterface $container): ApiInterface
    {
        $protocol = $container->get(ConfigInterface::class)->get('dtm-client.protocol');
        switch ($protocol) {
            case Protocol::HTTP:
                return $container->get(HttpApi::class);
            case Protocol::GRPC:
                return $container->get(GrpcApi::class);
            default:
                throw new UnsupportedException();
        }
    }
}
