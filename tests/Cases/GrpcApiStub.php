<?php

namespace DtmClientTest\Cases;

use DtmClient\Api\GrpcApi;
use Hyperf\GrpcClient\BaseClient;
use Hyperf\GrpcClient\UniversalGrpcClient;
use DtmClient\Grpc\GrpcClient;

class GrpcApiStub extends GrpcApi
{
    protected function getDtmClient(): GrpcClient
    {
        $stub = \Mockery::mock(GrpcClient::class);
        $response = new \DtmClient\Grpc\Message\DtmGidReply();
        $response->setGid('22');
        $stub->shouldReceive('newGid')->andReturn($response);
        return $stub;
    }
}