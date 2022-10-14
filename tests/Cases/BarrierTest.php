<?php

namespace DtmClientTest\Cases;

use DtmClient\Barrier;
use DtmClient\Constants\DbType;
use DtmClient\Constants\Protocol;
use DtmClient\Constants\TransType;
use DtmClient\MySqlBarrier;
use DtmClient\TransContext;
use Hyperf\Contract\ConfigInterface;

class BarrierTest extends AbstractTestCase
{
    public function testCall()
    {
        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->method('get')->willReturn(DbType::MySQL);

        $mySqlBarrier = $this->createMock(MySqlBarrier::class);

        $mySqlBarrier->method('call')->willReturn(true);

        $barrier = new Barrier($configInterface, $mySqlBarrier);
        $this->assertTrue($barrier->call(function () {
            return true;
        }));
    }

    public function testBarrierFrom()
    {
        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->method('get')->willReturn(Protocol::GRPC);

        $mySqlBarrier = $this->createMock(MySqlBarrier::class);

        $barrier = new Barrier($configInterface, $mySqlBarrier);
        $barrier->barrierFrom(TransType::TCC, 'gid', 'branchId', 'try', 'phase2Url', 'testDtm');

        $this->assertSame(TransContext::toArray(), [
            'trans_type' => 'tcc',
            'gid' => 'gid',
            'branch_id' => 'branchId',
            'op' => 'try',
            'phase2_url' => 'phase2Url',
            'dtm' => 'testDtm'
        ]);

    }
}