<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClientTest\Cases;

use DtmClient\Api\GrpcApi;
use DtmClient\Grpc\GrpcClient;
use GuzzleHttp\Client;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\ChannelPool;
use Hyperf\Utils\Coroutine;
use Mockery;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @coversNothing
 */
class GrpcApiTest extends AbstractTestCase
{
    public function testGrpcApiConstructor()
    {
        $this->assertTrue($this->createGrpcApi() instanceof GrpcApi);
    }

    public function testGenerateGid()
    {
        $grpcApi = $this->createGrpcApi();
        $result = $grpcApi->generateGid();
        $this->assertEquals(22, strlen($result));
    }

    protected function createGrpcApi(): GrpcApi
    {
        $container = $this->createContainer();
        $grpcApi = $container->get(GrpcApi::class);
        return $grpcApi;
    }

    protected function createContainer(): ContainerInterface|Mockery\MockInterface|Mockery\LegacyMockInterface
    {
        $config = Mockery::mock(ConfigInterface::class);
        $config->shouldReceive('get')->with('dtm.server', '127.0.0.1')->andReturn('127.0.0.1');
        $config->shouldReceive('get')->with('dtm.port.http', 36789)->andReturn(36789);
        $config->shouldReceive('get')->with('dtm.port.grpc', 36790)->andReturn(36790);
        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn($config);
        $container->shouldReceive('get')->with(ChannelPool::class)->andReturn(ChannelPool::getInstance());
        $container->shouldReceive('get')->with(GrpcApi::class)->andReturn(
            new GrpcApi($container->get(ConfigInterface::class))
        );
        ApplicationContext::setContainer($container);
        return $container;
    }
}
