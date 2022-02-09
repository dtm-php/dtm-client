<?php

namespace DtmClient\Middleware;


use DtmClient\Annotation\Barrier as BarrierAnnotation;
use DtmClient\Barrier;
use DtmClient\BarrierFactory;
use DtmClient\TransContext;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DtmMiddleware implements MiddlewareInterface
{
    protected Barrier $barrier;

    public function __construct(Barrier $barrier)
    {
        $this->barrier = $barrier;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $transType = $queryParams['trans_type'] ?? null;
        $gid = $queryParams['gid'] ?? null;
        $branchId = $queryParams['branch_id'] ?? null;
        $op = $queryParams['op'] ?? null;
        if ($transType && $gid && $branchId && $op) {
            $this->barrier->barrierFrom($transType, $gid, $branchId, $op);
        }

        /** @var Dispatched $dispatched */
        $dispatched = $request->getAttribute(Dispatched::class);

        if ($dispatched instanceof Dispatched) {
            [$class, $method] = $dispatched->handler->callback;

            $annotations = AnnotationCollector::getClassMethodAnnotation($class, $method);

            if (isset($annotations[BarrierAnnotation::class])) {
                $this->barrier->call();
            }
        }

        $this->barrier->call();

        return $handler->handle($request);
    }
}