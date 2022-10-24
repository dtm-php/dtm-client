<?php

namespace DtmClientTest\Cases;

use DtmClient\Api\ApiInterface;
use DtmClient\Api\RequestBranch;
use DtmClient\Barrier;
use DtmClient\BranchIdGeneratorInterface;
use DtmClient\Constants\Branch;
use DtmClient\Constants\Operation;
use DtmClient\Constants\Protocol;
use DtmClient\Constants\TransType;
use DtmClient\DbTransaction\DBTransactionInterface;
use DtmClient\DtmImp;
use DtmClient\Grpc\Message\DtmRequest;
use DtmClient\TransContext;
use DtmClient\XA;

class XATest extends AbstractTestCase
{
    public function testLocalTransaction()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);
        $branchIdGenerator = \Mockery::mock(BranchIdGeneratorInterface::class);
        $dtmImp = \Mockery::mock(DtmImp::class);
        $dbTransaction = \Mockery::mock(DBTransactionInterface::class);

        $dtmImp->shouldReceive('xaHandlePhase2')->andReturnUsing(function () {
            TransContext::set(static::class . '.xaHandlePhase2', 1);
            return true;
        });

        $api->shouldReceive('getProtocol')->andReturn(Protocol::HTTP);
        $api->shouldReceive('registerBranch')->andReturn(true);

        $xa = new XA($api, $barrier, $branchIdGenerator, $dtmImp, $dbTransaction);

        TransContext::setOp(Branch::BranchCommit);
        $xa->localTransaction(function () {
            TransContext::set(static::class . '.localTransaction', 1);
        });
        $this->assertEquals(1, TransContext::get(static::class . '.xaHandlePhase2'));
        $this->assertEquals(null, TransContext::get(static::class . '.localTransaction'));
        $this->cleanTransContext();


        $dtmImp->shouldReceive('xaHandleLocalTrans')->andReturnUsing(function ($callback) {
            $callback();
            TransContext::set(static::class . '.localTransaction', 1);
        });

        $xa->localTransaction(function () {
            TransContext::set(static::class . '.localTransaction', 1);
        });

        $this->assertEquals(1, TransContext::get(static::class . '.localTransaction'));
        $this->assertEquals('', TransContext::get(static::class . '.xaHandlePhase2'));
        $this->cleanTransContext();
    }

    public function testCallBranchUseHttp()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);
        $branchIdGenerator = \Mockery::mock(BranchIdGeneratorInterface::class);
        $dtmImp = \Mockery::mock(DtmImp::class);
        $dbTransaction = \Mockery::mock(DBTransactionInterface::class);

        $api->shouldReceive('getProtocol')->andReturn(Protocol::HTTP);
        $api->shouldReceive('transRequestBranch')->andReturnUsing(function ($data) {
            TransContext::set(static::class . '.testCallBranchUseHttp', $data);
        });

        $branchIdGenerator->shouldReceive('generateSubBranchId')->andReturn('testSubBranchId');

        $xa = new XA($api, $barrier, $branchIdGenerator, $dtmImp, $dbTransaction);

        $xa->callBranch('/test', ['test' => 'test']);

        $requestBranch = new RequestBranch();
        $requestBranch->body = ['test' => 'test'];
        $requestBranch->url = '/test';
        $requestBranch->phase2Url = '/test';
        $requestBranch->op = Operation::ACTION;
        $requestBranch->method = 'POST';
        $requestBranch->branchId = 'testSubBranchId';
        $requestBranch->branchHeaders = TransContext::getBranchHeaders();
        $this->assertEquals($requestBranch, TransContext::get(static::class . '.testCallBranchUseHttp'));
        $this->cleanTransContext();
    }

    public function testCallBranchUseJsonRpcHttp()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);
        $branchIdGenerator = \Mockery::mock(BranchIdGeneratorInterface::class);
        $dtmImp = \Mockery::mock(DtmImp::class);
        $dbTransaction = \Mockery::mock(DBTransactionInterface::class);

        $api->shouldReceive('getProtocol')->andReturn(Protocol::JSONRPC_HTTP);
        $api->shouldReceive('transRequestBranch')->andReturnUsing(function ($data) {
            TransContext::set(static::class . '.testCallBranchUseHttp', $data);
        });

        $branchIdGenerator->shouldReceive('generateSubBranchId')->andReturn('testSubBranchId');

        $xa = new XA($api, $barrier, $branchIdGenerator, $dtmImp, $dbTransaction);

        $xa->callBranch('/test', ['test' => 'test']);

        $requestBranch = new RequestBranch();
        $requestBranch->body = ['test' => 'test'];
        $requestBranch->url = '/test';
        $requestBranch->phase2Url = '/test';
        $requestBranch->op = Operation::ACTION;
        $requestBranch->method = 'POST';
        $requestBranch->branchId = 'testSubBranchId';
        $requestBranch->branchHeaders = TransContext::getBranchHeaders();
        $this->assertEquals($requestBranch, TransContext::get(static::class . '.testCallBranchUseHttp'));
        $this->cleanTransContext();
    }

    public function testCallBranchUseGrpc()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);
        $branchIdGenerator = \Mockery::mock(BranchIdGeneratorInterface::class);
        $dtmImp = \Mockery::mock(DtmImp::class);
        $dbTransaction = \Mockery::mock(DBTransactionInterface::class);

        $api->shouldReceive('getProtocol')->andReturn(Protocol::GRPC);
        $api->shouldReceive('transRequestBranch')->andReturnUsing(function ($data) {
            TransContext::set(static::class . '.testCallBranchUseHttp', $data);
        });

        $dtmRequest = new DtmRequest();
        $dtmRequest->setCustomedData(json_encode(['test' => 'test']));
        $branchIdGenerator->shouldReceive('generateSubBranchId')->andReturn('testSubBranchId');

        $xa = new XA($api, $barrier, $branchIdGenerator, $dtmImp, $dbTransaction);

        $xa->callBranch('/test', $dtmRequest, ['test' => 'test1']);

        $requestBranch = new RequestBranch();
        $requestBranch->url = '/test';
        $requestBranch->phase2Url = '/test';
        $requestBranch->op = Operation::ACTION;
        $requestBranch->grpcDeserialize = ['test' => 'test1'];
        $requestBranch->grpcArgument = $dtmRequest;
        $requestBranch->grpcMetadata = [
            'dtm-gid' => TransContext::getGid(),
            'dtm-trans_type' => TransType::XA,
            'dtm-branch_id' => 'testSubBranchId',
            'dtm-op' => Operation::ACTION,
            'dtm-dtm' => TransContext::getDtm(),
            'dtm-phase2_url' => '/test',
            'dtm-url' => '/test',
        ];
        $this->assertEquals($requestBranch, TransContext::get(static::class . '.testCallBranchUseHttp'));
        $this->cleanTransContext();
    }

    public function testGlobalTransaction()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);
        $branchIdGenerator = \Mockery::mock(BranchIdGeneratorInterface::class);
        $dtmImp = \Mockery::mock(DtmImp::class);
        $dbTransaction = \Mockery::mock(DBTransactionInterface::class);

        $api->shouldReceive('prepare')->andReturnTrue();
        $api->shouldReceive('submit')->andReturnTrue();
        $api->shouldReceive('abort')->andReturnTrue();

        $xa = new XA($api, $barrier, $branchIdGenerator, $dtmImp, $dbTransaction);

        $xa->globalTransaction('testGid', function () {
            TransContext::set(static::class . '.globalTransaction', 1);
        });
        $this->assertEquals(1,  TransContext::get(static::class . '.globalTransaction'));
        $this->assertEquals(Operation::ACTION,  TransContext::getOp());
        $this->assertEquals('testGid',  TransContext::getGid());
        $this->assertEquals(TransType::XA,  TransContext::getTransType());
        $this->assertEquals('',  TransContext::getBranchId());
        $this->cleanTransContext();

    }

    protected function cleanTransContext()
    {
        // Clean TransContext
        TransContext::setOp('');
        TransContext::set(static::class . '.localTransaction', null);
        TransContext::set(static::class . '.xaHandlePhase2', null);
        TransContext::set(static::class . '.testCallBranchUseHttp', null);
        TransContext::set(static::class . '.globalTransaction', null);
        TransContext::setGid('');
        TransContext::setTransType('');
        TransContext::setBranchId('');
    }

}