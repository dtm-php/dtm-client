<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Middleware;

use DtmClient\Barrier;
use DtmClient\TransContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DtmXaGrpcMiddleware implements MiddlewareInterface
{
    protected Barrier $barrier;

    public function __construct(Barrier $barrier)
    {
        $this->barrier = $barrier;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $headers = $request->getHeaders();
        $this->initTransContext($headers);
        return $handler->handle($request);
    }

    protected function initTransContext(array $params): void
    {
        $dtm = $this->getFistValue($params, 'dtm-dtm');
        $gid = $this->getFistValue($params, 'dtm-gid');
        $transType = $this->getFistValue($params, 'dtm-trans_type');
        $branchId = $this->getFistValue($params, 'dtm-branch_id');
        $op = $this->getFistValue($params, 'dtm-op');
        $phase2Url = $this->getFistValue($params, 'dtm-phase2_url');
        if ($transType && $gid && $branchId && $op) {
            $this->barrier->barrierFrom($transType, $gid, $branchId, $op);
        }
        $dtm && TransContext::setDtm($dtm);
        $phase2Url && TransContext::setPhase2URL($phase2Url);
    }

    protected function getFistValue(array $values, string $key): string|null
    {
        return $values[$key][0] ?? null;
    }
}
