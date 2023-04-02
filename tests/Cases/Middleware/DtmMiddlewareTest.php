<?php

namespace DtmClientTest\Cases\Middleware;

use DtmClient\Barrier;
use DtmClient\Middleware\DtmMiddleware;
use DtmClientTest\Cases\AbstractTestCase;
use Hyperf\Contract\ConfigInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Router\Dispatched;

class DtmMiddlewareTest extends AbstractTestCase
{
    public function testProcess()
    {
        $barrier = \Mockery::mock(Barrier::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $config = \Mockery::mock(ConfigInterface::class);
        $request = \Mockery::mock(ServerRequestInterface::class);
        $handler = \Mockery::mock(RequestHandlerInterface::class);


        $barrier->shouldReceive('barrierFrom')->andReturnTrue();

        $request->shouldReceive('getQueryParams')->andReturn([
            'trans_type' => '',
            'gid' => '',
            'branch_id' => '',
            'op' => '',
            'phase2_url' => '',
        ]);
        $request->shouldReceive('getHeaders')->andReturn([
            'dtm-trans_type' => ['mock-trans_type'],
            'dtm-gid' => ['mock-gid'],
            'dtm-branch_id' => ['mock-branch_id'],
            'dtm-op' => ['mock-op'],
            'dtm-phase2_url' => ['mock-phase2_url'],
            'dtm-dtm' => ['mock-dtm'],
        ]);

        $request->shouldReceive('getAttribute')->andReturn(new Dispatched());

        $middleware = new DtmMiddleware($barrier, $response, $config);


        $middleware->process($request, $handler);
    }
}