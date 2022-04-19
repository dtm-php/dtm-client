<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Middleware;

use Closure;
use DtmClient\Barrier;
use Hyperf\Contract\ConfigInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Psr\Http\Message\ResponseInterface;

/**
 * The middleware use in laravel framework.
 */
class DtmLaravelMiddleware
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

    public function handle(Request $request, Closure $next)
    {
        $barrier = $this->config->get('dtm.barrier.apply', []);

        $businessCall = function () use ($next, $request) {
            $next($request);
        };
        [$class, $method] = explode('@', Route::currentRouteAction());
        if (in_array($class . '::' . $method, $barrier) && $this->barrier->call($businessCall)) {
            return $this->response->withStatus(200);
        }
        return $next($request);
    }
}
