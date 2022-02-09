<?php

namespace DtmClient\Middleware;


use DtmClient\Annotation\Barrier as BarrierAnnotation;
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

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $transType = $queryParams['trans_type'] ?? null;
        $gid = $queryParams['gid'] ?? null;
        $branchId = $queryParams['branch_id'] ?? null;
        $op = $queryParams['op'] ?? null;
        if ($transType && $gid && $branchId && $op) {
            BarrierFactory::barrierFrom($transType, $gid, $branchId, $op);
        }

        /** @var Dispatched $dispatched */
        $dispatched = $request->getAttribute(Dispatched::class);

        [$class, $method] = $dispatched->handler->callback;

        $result = AnnotationCollector::get($class);

        if (! isset($result['_m'][$method][BarrierAnnotation::class])) {
            return $handler->handle($request);
        }

        BarrierFactory::call();

        return $handler->handle($request);
    }
}