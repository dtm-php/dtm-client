<?php

namespace DtmClientTest\Cases;

use DtmClient\Api\ApiInterface;
use DtmClient\Barrier;
use DtmClient\Constants\Protocol;
use DtmClient\Constants\TransType;
use DtmClient\Exception\FailureException;
use DtmClient\Grpc\Message\DtmRequest;
use DtmClient\Msg;
use DtmClient\TransContext;
use Exception;

class MsgTest extends AbstractTestCase
{

    public function testInit()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);

        $api->shouldReceive('generateGid')->andReturn('GidStub');

        $msg = new Msg($api, $barrier);
        $msg->init();
        $this->assertSame('GidStub', TransContext::getGid());
        $this->assertSame(TransType::MSG, TransContext::getTransType());
        $this->assertSame('', TransContext::getBranchId());

        $msg->init('test');
        $this->assertSame('test', TransContext::getGid());
    }

    public function testAddUseHttpProtocol()
    {
        \Mockery::resetContainer();
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);

        $api->shouldReceive('getProtocol')->andReturn(Protocol::HTTP);

        $msg = new Msg($api, $barrier);
        $payload = ['payload' => 'value'];
        $msg->add('testAction1', $payload);
        $this->assertSame(TransContext::getPayloads(), [json_encode($payload)]);
        $this->assertSame(TransContext::getSteps(), [['action' => 'testAction1']]);

        $msg->add('testAction2', $payload);
        $this->assertSame(TransContext::getPayloads(), [json_encode($payload), json_encode($payload)]);
        $this->assertSame(TransContext::getSteps(), [['action' => 'testAction1'],['action' => 'testAction2']]);

        // Clear TransContext
        TransContext::setPayloads([]);
        TransContext::setSteps([]);
    }

    public function testAddUseJsonRpcHttpProtocol()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);

        $api->shouldReceive('getProtocol')->andReturn(Protocol::JSONRPC_HTTP);

        $msg = new Msg($api, $barrier);
        $payload = ['payload' => 'value'];
        $msg->add('testAction1', $payload);
        $this->assertSame(TransContext::getPayloads(), [json_encode($payload)]);
        $this->assertSame(TransContext::getSteps(), [['action' => 'testAction1']]);

        $msg->add('testAction2', $payload);
        $this->assertSame(TransContext::getPayloads(), [json_encode($payload), json_encode($payload)]);
        $this->assertSame(TransContext::getSteps(), [['action' => 'testAction1'],['action' => 'testAction2']]);

        // Clear TransContext
        TransContext::setPayloads([]);
        TransContext::setSteps([]);
    }

    public function testAddUseGrpcProtocol()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);

        $api->shouldReceive('getProtocol')->andReturn(Protocol::GRPC);

        $msg = new Msg($api, $barrier);

        $payload1 = new DtmRequest();
        $payload1->setCustomedData('payload1');
        $msg->add('testAction1', $payload1);

        $this->assertSame(TransContext::getBinPayloads(), [$payload1->serializeToString()]);
        $this->assertSame(TransContext::getSteps(), [['action' => 'testAction1']]);

        $payload2 = new DtmRequest();
        $payload2->setCustomedData('payload2');
        $msg->add('testAction2', $payload2);
        $this->assertSame(TransContext::getBinPayloads(), [$payload1->serializeToString(), $payload2->serializeToString()]);
        $this->assertSame(TransContext::getSteps(), [['action' => 'testAction1'],['action' => 'testAction2']]);
    }

    public function testPrepare()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);

        $api->shouldReceive('prepare')->andReturn('success');

        $msg = new Msg($api, $barrier);
        $result = $msg->prepare('testQueryPrepared');
        $this->assertSame('testQueryPrepared', TransContext::getQueryPrepared());
        $this->assertSame('success', $result);
    }

    public function testSubmit()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);

        $api->shouldReceive('submit')->andReturn('success');

        $msg = new Msg($api, $barrier);
        $result = $msg->submit();
        $this->assertSame('success', $result);
    }

    public function testDoAndSubmit()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);

        $barrier->shouldReceive('barrierFrom')->andReturnTrue();

        $api->shouldReceive('prepare')->andReturn('success');
        $api->shouldReceive('submit')->andReturn('success');

        $msg = new Msg($api, $barrier);
        $msg->doAndSubmit(
            'testQuery',
            function () {
                TransContext::set(static::class . 'DoAndSubmit', 1);
            }
        );
        $this->assertSame(1, TransContext::get(static::class . 'DoAndSubmit'));
    }

    public function testDoAndSubmitThrowFailure()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);

        $barrier->shouldReceive('barrierFrom')->andReturnTrue();

        $api->shouldReceive('prepare')->andReturn('success');
        $api->shouldReceive('submit')->andReturn('success');
        $api->shouldReceive('abort')->andReturnUsing(function ($data) {
            TransContext::set(static::class . 'abort', $data);
        });


        $msg = new Msg($api, $barrier);
        $isThrowFailureException = 0;
        try {
            $msg->doAndSubmit(
                'testQuery',
                function () {
                    throw new FailureException();
                }
            );
        } catch (FailureException $exception) {
            $isThrowFailureException = 1;
        }

        $this->assertSame(1, $isThrowFailureException);
        $this->assertSame([
            'gid' => TransContext::getGid(),
            'trans_type' => TransType::MSG,
        ], TransContext::get(static::class . 'abort'));

    }

    public function testDoAndSubmitThrowException()
    {
        $api = \Mockery::mock(ApiInterface::class);
        $barrier = \Mockery::mock(Barrier::class);

        $barrier->shouldReceive('barrierFrom')->andReturnTrue();

        $api->shouldReceive('prepare')->andReturn('success');
        $api->shouldReceive('submit')->andReturn('success');
        $api->shouldReceive('abort')->andReturn('success');
        $api->shouldReceive('transRequestBranch')->andReturn('transRequestBranchSuccess');

        $msg = new Msg($api, $barrier);
        $isThrowException = 0;
        try {
            $msg->doAndSubmit(
                'testQuery',
                function () {
                    throw new FailureException();
                }
            );
        } catch (Exception $exception) {
            $isThrowException = 1;
        }

        $this->assertSame(1, $isThrowException);
    }

}