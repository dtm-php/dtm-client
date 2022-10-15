<?php

namespace DtmClientTest\Cases;

use DtmClient\Api\ApiInterface;
use DtmClient\Api\RequestBranch;
use DtmClient\BranchIdGeneratorInterface;
use DtmClient\Constants\Operation;
use DtmClient\Constants\Protocol;
use DtmClient\Constants\TransType;
use DtmClient\Grpc\Message\DtmRequest;
use DtmClient\TCC;
use DtmClient\TransContext;
use Google\Protobuf\Internal\Message;

class TCCTest extends AbstractTestCase
{
    public function testInit()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $branchIdGenerator = \Mockery::mock(BranchIdGeneratorInterface::class);

        $api->shouldReceive('generateGid')->andReturn('GidStub');

        $tcc = new TCC($api, $branchIdGenerator);
        $tcc->init();
        $this->assertSame('GidStub', TransContext::getGid());
        $this->assertSame(TransType::TCC, TransContext::getTransType());
        $this->assertSame('', TransContext::getBranchId());

        $tcc->init('test');
        $this->assertSame('test', TransContext::getGid());
    }

    public function testGlobalTransaction()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $branchIdGenerator = \Mockery::mock(BranchIdGeneratorInterface::class);

        $api->shouldReceive('generateGid')->andReturn('GlobalTransactionGidStub');
        $api->shouldReceive('prepare')->andReturnTrue();
        $api->shouldReceive('abort')->andReturnTrue();
        $api->shouldReceive('submit')->andReturnTrue();

        $tcc = new TCC($api, $branchIdGenerator);

        $tcc->globalTransaction(function () {
            // Used only in test cases
            TransContext::set(static::class . 'globalTransaction', 1);
        }, 'GidStub');
        $this->assertSame('GidStub', TransContext::getGid());
        $this->assertSame(TransType::TCC, TransContext::getTransType());
        $this->assertSame('', TransContext::getBranchId());
        $this->assertSame(1, TransContext::get(static::class . 'globalTransaction'));
    }

    public function testCallBranchUseJsonRpcProtocol()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $branchIdGenerator = \Mockery::mock(BranchIdGeneratorInterface::class);

        $branchIdGenerator->shouldReceive('generateSubBranchId')->andReturn('BranchIdStub');

        $api->shouldReceive('getProtocol')->andReturn(Protocol::JSONRPC_HTTP);
        $api->shouldReceive('registerBranch')->andReturnUsing(function ($data) {
            TransContext::set(static::class . 'registerBranchBody', $data);
        });
        $api->shouldReceive('transRequestBranch')->andReturnUsing(function ($data) {
            return $data;
        });

        TransContext::setGid('testGid');

        $tcc = new TCC($api, $branchIdGenerator);

        $response = $tcc->callBranch(
            ['data' => 'test'],
            '127.0.0.1/try',
            '127.0.0.1/confirm',
            '127.0.0.1/cancel'
        );

        $branchRequest = new RequestBranch();
        $branchRequest->method = 'POST';
        $branchRequest->url = '127.0.0.1/try';
        $branchRequest->branchId = 'BranchIdStub';
        $branchRequest->op = Operation::TRY;
        $branchRequest->body = ['data' => 'test'];

        $this->assertEquals($branchRequest, $response);
        $this->assertSame([
            'data' => json_encode(['data' => 'test']),
            'branch_id' => 'BranchIdStub',
            'confirm' => '127.0.0.1/confirm',
            'cancel' => '127.0.0.1/cancel',
            'gid' => 'testGid',
            'trans_type' => TransType::TCC,
        ], TransContext::get(static::class . 'registerBranchBody'));
    }

    public function testCallBranchUseHttpProtocol()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $branchIdGenerator = \Mockery::mock(BranchIdGeneratorInterface::class);

        $branchIdGenerator->shouldReceive('generateSubBranchId')->andReturn('BranchIdStub');

        $api->shouldReceive('getProtocol')->andReturn(Protocol::HTTP);
        $api->shouldReceive('registerBranch')->andReturnUsing(function ($data) {
            TransContext::set(static::class . 'registerBranchBody', $data);
        });
        $api->shouldReceive('transRequestBranch')->andReturnUsing(function ($data) {
            return $data;
        });

        TransContext::setGid('testGid');

        $tcc = new TCC($api, $branchIdGenerator);

        $response = $tcc->callBranch(
            ['data' => 'test'],
            '127.0.0.1/try',
            '127.0.0.1/confirm',
            '127.0.0.1/cancel'
        );

        $branchRequest = new RequestBranch();
        $branchRequest->method = 'POST';
        $branchRequest->url = '127.0.0.1/try';
        $branchRequest->branchId = 'BranchIdStub';
        $branchRequest->op = Operation::TRY;
        $branchRequest->body = ['data' => 'test'];

        $this->assertEquals($branchRequest, $response);
        $this->assertSame([
            'data' => json_encode(['data' => 'test']),
            'branch_id' => 'BranchIdStub',
            'confirm' => '127.0.0.1/confirm',
            'cancel' => '127.0.0.1/cancel',
            'gid' => 'testGid',
            'trans_type' => TransType::TCC,
        ], TransContext::get(static::class . 'registerBranchBody'));
    }

    public function testCallBranchUseGrpcProtocol()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $branchIdGenerator = \Mockery::mock(BranchIdGeneratorInterface::class);

        $branchIdGenerator->shouldReceive('generateSubBranchId')->andReturn('BranchIdStub');

        $api->shouldReceive('getProtocol')->andReturn(Protocol::GRPC);
        $api->shouldReceive('registerBranch')->andReturnUsing(function ($data) {
            TransContext::set(static::class . 'registerBranchBody', $data);
        });
        $api->shouldReceive('transRequestBranch')->andReturnUsing(function ($data) {
            return $data;
        });

        TransContext::setGid('testGid');

        $tcc = new TCC($api, $branchIdGenerator);

        $message = new DtmRequest();

        $response = $tcc->callBranch(
            $message,
            '127.0.0.1/try',
            '127.0.0.1/confirm',
            '127.0.0.1/cancel'
        );

        $branchRequest = new RequestBranch();
        $branchRequest->grpcArgument = $message;
        $branchRequest->url = '127.0.0.1/try';
        $branchRequest->grpcMetadata = [
            'dtm-gid' => 'testGid',
            'dtm-trans_type' => TransType::TCC,
            'dtm-branch_id' => 'BranchIdStub',
            'dtm-op' => Operation::TRY,
            'dtm-dtm' => TransContext::getDtm(),
        ];

        $this->assertEquals($branchRequest, $response);
        $this->assertSame([
            'Gid' => 'testGid',
            'TransType' => TransType::TCC,
            'BranchID' => 'BranchIdStub',
            'BusiPayload' => $message->serializeToString(),
            'Data' => ['confirm' => '127.0.0.1/confirm', 'cancel' => '127.0.0.1/cancel'],
        ], TransContext::get(static::class . 'registerBranchBody'));
    }
}