<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Api\ApiInterface;

class TCC
{
    protected ApiInterface $api;

    protected array $branch = [];

    protected BranchIdGenerateInterface $branchIdGenerate;

    public function __construct(ApiInterface $api, BranchIdGenerateInterface $branchIdGenerate)
    {
        $this->api = $api;
        $this->branchIdGenerate = $branchIdGenerate;
    }

    public function generateGid(): string
    {
        return $this->api->generateGid();
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
            'cancel' => $cancelUrl,
        ]);
    }
}
