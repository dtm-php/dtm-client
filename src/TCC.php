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

class TCC extends AbstractTransaction
{
    protected BranchIdGeneratorInterface $branchIdGenerator;

    public function __construct(ApiInterface $api, BranchIdGenerateInterface $branchIdGenerate)
    {
        $this->api = $api;
        $this->branchIdGenerator = $branchIdGenerate;
    }

    public function globalTransaction(callable $callback, ?string $gid = null)
    {
        if ($gid === null) {
            $gid = $this->generateGid();
        }
        TransContext::init($gid, TransType::TCC, '');
        $requestBody = TransContext::toArray();
        try {
            $this->api->prepare($requestBody);
            $callback($this);
        } catch (\Throwable $throwable) {
            $this->api->abort($requestBody);
            throw $throwable;
        }

        $this->api->submit($requestBody);
    }

    public function callBranch(array $body, string $tryUrl, string $confirmUrl, string $cancelUrl)
    {
        $branchId = $this->branchIdGenerator->generateSubBranchId();
        $this->api->registerBranch([
            'data' => json_encode($body),
            'branch_id' => $branchId,
            'confirm' => $confirmUrl,
            'cancel' => $cancelUrl,
            'gid' => TransContext::getGid(),
            'trans_type' => TransType::TCC,
        ]);

        return $this->api->transRequestBranch('POST', $body, $branchId, Operation::TRY, $tryUrl);
    }
}
