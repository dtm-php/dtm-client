<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */

namespace DtmClient;

use DtmClient\Api\ApiInterface;
use DtmClient\Constants\TransType;

class Sage
{

    protected array $orders = [];

    protected bool $concurrent = false;

    protected ApiInterface $api;

    public function __construct(ApiInterface $api)
    {
        $this->api = $api;
    }

    public function init(string $gid)
    {
        TransContext::init($gid, 'sage', '');
    }

    public function add(string $action, string $compensate, array|object $payload): static
    {
        TransContext::appendSteps([
            'action' => $action,
            'compensate' => $compensate,
        ]);
        TransContext::appendPayloads(json_encode($payload));
        return $this;
    }

    public function addBranchOrder(int $branch, array $preBranches): static
    {
        $this->orders[$branch] = $preBranches;
        return $this;
    }

    public function enableConcurrent()
    {
        $this->concurrent = true;
    }

    public function submit()
    {
        $this->addConcurrentContext();
        return $this->api->submit([
            'gid' => TransContext::getGid(),
            'trans_type' => TransType::SAGE,
            'payloads' => TransContext::getPayloads(),
            'steps' => TransContext::getSteps(),
        ]);
    }

    public function addConcurrentContext()
    {
        if ($this->concurrent) {
            TransContext::setCustomData(json_encode([
                'concurrent' => $this->concurrent,
                'orders' => $this->orders,
            ]));
        }
    }

}
