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
