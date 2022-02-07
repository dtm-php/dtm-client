<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Api\ApiInterface;

class TCC
{
    protected ApiInterface $api;

    protected array $branch = [];

    public function __construct(ApiFactory $apiFactory)
    {
        $this->api = $apiFactory->create();
    }

    public function generateGid(string $dtmService): string
    {
        return $this->api->generateGid($dtmService);
    }

    public function tccGlobalTransaction(string $dtmServer, string $gid, callable $callback)
    {
    }

    public function callBranch(array $body, string $tryUrl, string $confirmUrl, string $cancelUrl)
    {

    }
}
