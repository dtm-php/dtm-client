<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Api\ApiInterface;
use DtmClient\Constants\Operation;
use DtmClient\Constants\TransType;
use DtmClient\Exception\RequestException;

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

    public function tccGlobalTransaction(string $gid, callable $callback)
    {
        TransContext::init($gid, TransType::TCC, '');

        try {
            $callback();
            $this->api->prepare([
                'gid' => $gid,
                'trans_type' => TransType::TCC,
            ]);
        } catch (\Throwable $throwable) {
            $this->api->abort([
                'gid' => $gid,
                'trans_type' => TransType::TCC,
            ]);
            throw $throwable;
        }

        $this->api->submit([
            'gid' => $gid,
            'trans_type' => 'tcc',
        ]);
    }

    public function tccFromQuery()
    {
        $gid = TransContext::getGid();
        $branchId = TransContext::getBranchId();
        if (empty($gid)) {
            throw new RequestException(sprintf('bad tcc info. gid: %s parentID: %s', $gid, $branchId));
        }
        return $this->api->query(['gid' => $gid]);
    }

    public function callBranch(array $body, string $tryUrl, string $confirmUrl, string $cancelUrl)
    {
        $branchId = $this->branchIdGenerate->generateSubBranchId();
        $this->api->registerBranch([
            'data' => $body,
            'branch_id' => $branchId,
            'confirm' => $confirmUrl,
            'cancel' => $cancelUrl,
        ]);

        $this->api->transRequestBranch('POST', $body, $branchId, Operation::TRY, $tryUrl);
    }
}
