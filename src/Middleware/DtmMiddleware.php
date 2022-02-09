<?php

namespace DtmClient\Middleware;


use DtmClient\Barrier;
use DtmClient\TransContext;
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
            Barrier::barrierFrom($transType, $gid, $branchId, $op);
        }
        $response = $handler->handle($request);
        return $response;
    }
}