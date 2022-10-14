<?php

namespace DtmClientTest\Cases;

use DtmClient\Api\GrpcApi;
use DtmClient\Api\HttpApi;
use DtmClient\Api\JsonRpcHttpApi;
use DtmClient\ApiFactory;
use DtmClient\Constants\Protocol;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class ApiFactoryTest extends AbstractTestCase
{
    public function testGetHttpApi()
    {
        $configInterface = \Mockery::mock(ConfigInterface::class);
        $configInterface->shouldReceive('get')->andReturn(Protocol::HTTP);
        $container = \Mockery::mock(ContainerInterface::class);

        $container->shouldReceive('get')->withArgs([ConfigInterface::class])->andReturn($configInterface);

        $httpApi = \Mockery::mock(HttpApi::class);
        $container->shouldReceive('get')->withArgs([HttpApi::class])->andReturn($httpApi);


        $api = (new ApiFactory())($container);
        $this->assertInstanceOf(HttpApi::class, $api);
    }

    public function testGetGrpcApi()
    {
        $configInterface = \Mockery::mock(ConfigInterface::class);
        $configInterface->shouldReceive('get')->andReturn(Protocol::GRPC);
        $container = \Mockery::mock(ContainerInterface::class);

        $container->shouldReceive('get')->withArgs([ConfigInterface::class])->andReturn($configInterface);

        $httpApi = \Mockery::mock(GrpcApi::class);
        $container->shouldReceive('get')->withArgs([GrpcApi::class])->andReturn($httpApi);


        $api = (new ApiFactory())($container);
        $this->assertInstanceOf(GrpcApi::class, $api);
    }

    public function testGetJsonRpcHttpApi()
    {
        $configInterface = \Mockery::mock(ConfigInterface::class);
        $configInterface->shouldReceive('get')->andReturn(Protocol::JSONRPC_HTTP);
        $container = \Mockery::mock(ContainerInterface::class);

        $container->shouldReceive('get')->withArgs([ConfigInterface::class])->andReturn($configInterface);

        $httpApi = \Mockery::mock(JsonRpcHttpApi::class);
        $container->shouldReceive('get')->withArgs([JsonRpcHttpApi::class])->andReturn($httpApi);


        $api = (new ApiFactory())($container);
        $this->assertInstanceOf(JsonRpcHttpApi::class, $api);
    }
}