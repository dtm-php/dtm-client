<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Api\ApiInterface;
use DtmClient\Api\HttpApi;
use DtmClient\Api\HttpApiFactory;
use DtmClient\Config\DatabaseConfigInterface;
use DtmClient\Config\HyperfDatabaseConfig;
use DtmClient\DBSpecial\DBSpecialFactory;
use DtmClient\DBSpecial\DBSpecialInterface;
use DtmClient\DbTransaction\DBTransactionInterface;
use DtmClient\DbTransaction\HyperfDbTransaction;
use DtmClient\Grpc\GrpcClientManager;
use DtmClient\Grpc\GrpcClientManagerFactory;
use DtmClient\JsonRpc\DtmPathGenerator;
use Hyperf\HttpServer\Response;
use Hyperf\JsonRpc\JsonRpcPoolTransporter;
use Hyperf\JsonRpc\JsonRpcTransporter;
use Hyperf\Rpc\Contract\PathGeneratorInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\GrpcClient\BaseClient;
use Hyperf\GrpcServer\CoreMiddleware;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for dtm client.',
                    'source' => __DIR__ . '/../publish/dtm.php',
                    'destination' => BASE_PATH . '/config/autoload/dtm.php',
                ],
            ],
            'dependencies' => [
                HttpApi::class => HttpApiFactory::class,
                BranchIdGeneratorInterface::class => BranchIdGenerator::class,
                ApiInterface::class => ApiFactory::class,
                GrpcClientManager::class => GrpcClientManagerFactory::class,
                DBTransactionInterface::class => HyperfDbTransaction::class,
                DBSpecialInterface::class => DBSpecialFactory::class,
                PathGeneratorInterface::class => DtmPathGenerator::class,
                JsonRpcTransporter::class => JsonRpcPoolTransporter::class,
                ResponseInterface::class => Response::class,
                DatabaseConfigInterface::class => HyperfDatabaseConfig::class,
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
        ];
    }
}
