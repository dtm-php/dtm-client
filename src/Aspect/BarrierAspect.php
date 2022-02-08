<?php

namespace DtmClient\Aspect;

use DtmClient\Annotation\Barrier;
use DtmClient\TransContext;
use Hyperf\DB\DB;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Psr\Http\Message\RequestInterface;

#[Aspect]
class BarrierAspect extends AbstractAspect
{
  
    public $annotations = [
        Barrier::class,
    ];

    protected $opMap = [
        'cancel' => 'try',
        'compensate' => 'action'
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /** @var RequestInterface $request */
        $request = Context::get(RequestInterface::class);

        $inputs = $request->all();

        $op = $inputs[0]['op'];
        $gid = $inputs[0]['gid'];
        $branchId = $inputs[0]['branch_id'];
        $transType = $inputs[0]['trans_type'];
        TransContext::setGid();
        TransContext::setBranchId($branchId);
        TransContext::setTransType($transType);
        TransContext::setOp($op);

        TransContext::setBarrierID(TransContext::getBarrierID() + 1);
        $barrierID = TransContext::getBarrierID();
        $bid = sprintf('%02d', $barrierID);

        $originOP = $opMap[$op] ?? '';
        DB::beginTransaction();
        try {
            \DtmClient\Barrier::insertBarrier($transType, $gid, $branchId, $originOP, $bid, $barrierID);
            \DtmClient\Barrier::insertBarrier($transType, $gid, $branchId, $op, $bid, $barrierID);
            $result = $proceedingJoinPoint->process();
            DB::commit();
            return $result;
        } catch (\Throwable $throwable) {
            DB::rollback();
            throw $throwable;
        }


    }

}