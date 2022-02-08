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

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        return \DtmClient\Barrier::call(function () use ($proceedingJoinPoint) {
            return $proceedingJoinPoint->process();
        });
    }

}