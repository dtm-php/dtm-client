<?php

namespace DtmClient\Middleware;

use DtmClient\Barrier;
use DtmClient\TransContext;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\RouteCollector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Router\Dispatched;
use DtmClient\Annotation\Barrier as BarrierAnnotation;

class BarrierMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        /** @var Dispatched $dispatched */
        $dispatched = $request->getAttribute(Dispatched::class);

        [$class, $method] = $dispatched->handler->callback;

        if (! isset($res['_m'][$method][BarrierAnnotation::class])) {
            return $handler->handle($request);
        }

        return Barrier::call(function () use ($request, $handler) {
            $requestBody = $request->getBody();
            $inputs = json_decode($requestBody->getContents(), true);
            $op = $inputs[0]['op'] ?? $inputs['op'] ?? '';
            $gid = $inputs[0]['gid']?? $inputs['gid']?? 0;
            $branchId = $inputs[0]['branch_id']?? $inputs['branch_id'] ?? '';
            $transType = $inputs[0]['trans_type']?? $inputs['trans_type']?? '';
            TransContext::setGid($gid);
            TransContext::setBranchId($branchId);
            TransContext::setTransType($transType);
            TransContext::setOp($op);
            $request->withBody($requestBody);
            
            return $handler->handle($request);
        });

    }
}