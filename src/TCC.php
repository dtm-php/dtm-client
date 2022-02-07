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

    protected BranchIdGenerateInterface $branchIdGenerate;

    public function __construct(ApiFactory $apiFactory, BranchIdGenerateInterface $branchIdGenerate)
    {
        $this->api = $apiFactory->create();
        $this->branchIdGenerate = $branchIdGenerate;
    }

    public function generateGid(string $dtmService): string
    {
        return $this->api->generateGid($dtmService);
    }

    public function tccGlobalTransaction(string $dtmServer, string $gid, callable $callback)
    {
        $callback();

    }

    public function callBranch(array $body, string $tryUrl, string $confirmUrl, string $cancelUrl)
    {
        $res = $this->api->registerBranch([
            'data' => $body,
            'branch_id' => $this->branchIdGenerate->generateSubBranchId(),
            'confirm' => $confirmUrl,
            'cancel' => $cancelUrl
        ]);
    }
}
