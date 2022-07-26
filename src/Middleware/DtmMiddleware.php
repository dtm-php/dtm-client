<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Middleware;

use DtmClient\Annotation\Barrier as BarrierAnnotation;
use DtmClient\Barrier;
use DtmClient\TransContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DtmMiddleware implements MiddlewareInterface
{
    protected Barrier $barrier;

    protected ResponseInterface $response;

    protected ConfigInterface $config;

    public function __construct(Barrier $barrier, ResponseInterface $response, ConfigInterface $config)
    {
        $this->barrier = $barrier;
        $this->response = $response;
        $this->config = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $queryParams = $request->getQueryParams() ?: $request->getParsedBody();
        $transType = $queryParams['trans_type'] ?? null;
        $gid = $queryParams['gid'] ?? null;
        $branchId = $queryParams['branch_id'] ?? null;
        $op = $queryParams['op'] ?? null;
        $phase2Url = $queryParams['phase2_url'] ?? null;
        if ($transType && $gid && $branchId && $op) {
            $this->barrier->barrierFrom($transType, $gid, $branchId, $op);
        }
        // use in XA
        $phase2Url && TransContext::setPhase2URL($phase2Url);

        /** @var Dispatched $dispatched */
        $dispatched = $request->getAttribute(Dispatched::class);
        if ($dispatched instanceof Dispatched && ! empty($dispatched->handler->callback)) {
            $callback = $dispatched->handler->callback;

            if (is_array($callback)) {
                [$class, $method] = $callback;
            }

            if (is_string($callback) && str_contains($callback, '@')) {
                [$class, $method] = explode('@', $callback);
            }

            if (is_string($callback) && str_contains($callback, '::')) {
                [$class, $method] = explode('::', $callback);
            }

            $barrier = $this->config->get('dtm.barrier.apply', []);

            $businessCall = function () use ($handler, $request) {
                $handler->handle($request);
            };

            if (in_array($class . '::' . $method, $barrier) && $this->barrier->call($businessCall)) {
                return $this->response->withStatus(200);
            }

            $annotations = AnnotationCollector::getClassMethodAnnotation($class, $method);

            if (isset($annotations[BarrierAnnotation::class]) && $this->barrier->call($businessCall)) {
                return $this->response->withStatus(200);
            }
        }

        return $handler->handle($request);
    }
}
