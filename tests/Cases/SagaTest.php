<?php

namespace DtmClientTest\Cases;

use DtmClient\Api\ApiInterface;
use DtmClient\Barrier;
use DtmClient\Constants\Protocol;
use DtmClient\Constants\TransType;
use DtmClient\Grpc\Message\DtmBranchRequest;
use DtmClient\Saga;
use DtmClient\TransContext;
use PHPUnit\Util\Json;

class SagaTest extends AbstractTestCase
{
    public function testInit()
    {
        $api = \Mockery::mock(ApiInterface::class);

        $api->shouldReceive('generateGid')->andReturn('GidStub');

        $saga = new Saga($api);
        $saga->init();
        $this->assertSame('GidStub', TransContext::getGid());
        $this->assertSame(TransType::SAGA, TransContext::getTransType());
        $this->assertSame('', TransContext::getBranchId());

        $saga->init('test');
        $this->assertSame('test', TransContext::getGid());
    }

    public function testAddUseHttp()
    {
        $api = \Mockery::mock(ApiInterface::class);

        $api->shouldReceive('getProtocol')->andReturn(Protocol::HTTP);

        $saga = new Saga($api);

        $result = $saga->add('testAction', 'compensate', ['test' => 'message']);
        $this->assertEquals($saga, $result);
        $this->assertEquals(TransContext::getSteps(), [['action' => 'testAction', 'compensate' => 'compensate']]);
        $this->assertEquals(TransContext::getPayloads(), [json_encode(['test' => 'message'])]);
        $this->cleanTransContext();
    }

    public function testAddUseJsonRpcHttp()
    {
        $api = \Mockery::mock(ApiInterface::class);

        $api->shouldReceive('getProtocol')->andReturn(Protocol::JSONRPC_HTTP);

        $saga = new Saga($api);

        $result = $saga->add('testAction', 'compensate', ['test' => 'message']);
        $this->assertEquals($saga, $result);
        $this->assertEquals(TransContext::getSteps(), [['action' => 'testAction', 'compensate' => 'compensate']]);
        $this->assertEquals(TransContext::getPayloads(), [json_encode(['test' => 'message'])]);
        $this->cleanTransContext();
    }

    public function testAddUseGrpc()
    {
        $api = \Mockery::mock(ApiInterface::class);

        $api->shouldReceive('getProtocol')->andReturn(Protocol::GRPC);

        $saga = new Saga($api);

        $payload = new DtmBranchRequest();
        $payload->setData(['test' => 'message']);
        $result = $saga->add('testAction', 'compensate', $payload);
        $this->assertEquals($saga, $result);
        $this->assertEquals(TransContext::getSteps(), [['action' => 'testAction', 'compensate' => 'compensate']]);
        $this->assertEquals(TransContext::getBinPayloads(), [$payload->serializeToString()]);
    }

    public function testAddBranchOrder()
    {
        $api = \Mockery::mock(ApiInterface::class);

        $saga = new Saga($api);

        $saga->addBranchOrder(1, ['preBranches']);
        $saga->addBranchOrder(2, ['preBranches1']);

        $ordersProperty = new \ReflectionProperty($saga, 'orders');
        $ordersProperty->setAccessible(true);
        $orders = $ordersProperty->getValue($saga);
        $this->assertEquals([1 => ['preBranches'], 2 => ['preBranches1']], $orders);
    }

    public function testEnableConcurrent()
    {
        $api = \Mockery::mock(ApiInterface::class);

        $saga = new Saga($api);

        $saga->enableConcurrent();

        $concurrentProperty = new \ReflectionProperty($saga, 'concurrent');
        $concurrentProperty->setAccessible(true);
        $concurrent = $concurrentProperty->getValue($saga);
        $this->assertTrue($concurrent);
    }

    public function testSubmit()
    {
        $api = \Mockery::mock(ApiInterface::class);

        $api->shouldReceive('submit')->andReturnTrue();

        $saga = new Saga($api);

        $result = $saga->submit();
        $this->assertTrue($result);
        $this->assertSame(null, TransContext::getCustomData());

        $saga->enableConcurrent();
        $saga->addBranchOrder(0, ['test' => 'test']);
        $result = $saga->submit();
        $this->assertTrue($result);
        $this->assertSame(json_encode([
            'concurrent' => true,
            'orders' => [
                ['test' => 'test']
            ],
        ]), TransContext::getCustomData());
    }

    public function cleanTransContext()
    {
        // Clean TransContext
        TransContext::setSteps([]);
        TransContext::setPayloads([]);
    }

}