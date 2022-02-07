<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClientTest\Cases;

use DtmClient\Api\HttpApi;
use DtmClient\Api\HttpApiFactory;
use GuzzleHttp\Client;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;
use Mockery;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @coversNothing
 */
class HttpApiTest extends AbstractTestCase
{
    public function testHttpApiConstructor()
    {
        $this->assertTrue($this->createHttpApi() instanceof HttpApi);
    }

    public function testGenerateGid()
    {
        $httpApi = $this->createHttpApi();
        $result = $httpApi->generateGid();
        $this->assertEquals(22, strlen($result));
    }

    protected function createHttpApi(): HttpApi
    {
        $container = $this->createContainer();
        $httpApi = $container->get(HttpApi::class);
        return $httpApi;
    }

    protected function createContainer(): ContainerInterface|Mockery\MockInterface|Mockery\LegacyMockInterface
    {
        $config = Mockery::mock(ConfigInterface::class);
        $config->shouldReceive('get')->with('dtm-client.server')->andReturn('127.0.0.1');
        $config->shouldReceive('get')->with('dtm-client.port.http')->andReturn(36789);
        $config->shouldReceive('get')->with('dtm-client.port.grpc')->andReturn(36790);
        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn($config);
        $httpApiFactory = new HttpApiFactory();
        $container->shouldReceive('get')->with(ClientFactory::class)->andReturn(new ClientFactory($container));
        $container->shouldReceive('get')->with(Client::class)->andReturn(
            $httpApiFactory($container)
        );
        $container->shouldReceive('get')->with(HttpApi::class)->andReturn(
            new HttpApi($container->get(Client::class), $container->get(ConfigInterface::class))
        );
        return $container;
    }
}
