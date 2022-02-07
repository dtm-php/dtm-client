<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use Hyperf\Contract\IdGeneratorInterface;

class TransBase
{
    public string $gid;

    public string $trans_type;

    public string $custom_data;

    public string $wait_result;

    // for trans type: xa, tcc
    public string $timeout_to_fail;

    // for trans type: msg saga xa tcc
    public string $retry_interval;

    public string $passthrough_headers;

    public string $branch_headers;

    public array $steps;

    /**
     * @var string[]
     */
    public array $payloads;

    // use in MSG/SAGA
    public array $binPayloads;

    // // used in XA/TCC
    public IdGeneratorInterface $idGenerator;

    // used in XA/TCC
    public string $op;

    // used in MSG
    public array $query_prepared;

    public function getGid(): string
    {
        return $this->gid;
    }

    public function setGid(string $gid): TransBase
    {
        $this->gid = $gid;
        return $this;
    }

    public function getTransType(): string
    {
        return $this->trans_type;
    }

    public function setTransType(string $trans_type): TransBase
    {
        $this->trans_type = $trans_type;
        return $this;
    }

    public function getCustomData(): string
    {
        return $this->custom_data;
    }

    public function setCustomData(string $custom_data): TransBase
    {
        $this->custom_data = $custom_data;
        return $this;
    }

    public function getWaitResult(): string
    {
        return $this->wait_result;
    }

    public function setWaitResult(string $wait_result): TransBase
    {
        $this->wait_result = $wait_result;
        return $this;
    }

    public function getTimeoutToFail(): string
    {
        return $this->timeout_to_fail;
    }

    public function setTimeoutToFail(string $timeout_to_fail): TransBase
    {
        $this->timeout_to_fail = $timeout_to_fail;
        return $this;
    }

    public function getRetryInterval(): string
    {
        return $this->retry_interval;
    }

    public function setRetryInterval(string $retry_interval): TransBase
    {
        $this->retry_interval = $retry_interval;
        return $this;
    }

    public function getPassthroughHeaders(): string
    {
        return $this->passthrough_headers;
    }

    public function setPassthroughHeaders(string $passthrough_headers): TransBase
    {
        $this->passthrough_headers = $passthrough_headers;
        return $this;
    }

    public function getBranchHeaders(): string
    {
        return $this->branch_headers;
    }

    public function setBranchHeaders(string $branch_headers): TransBase
    {
        $this->branch_headers = $branch_headers;
        return $this;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function setSteps(array $steps): TransBase
    {
        $this->steps = $steps;
        return $this;
    }

    /**
     * @param mixed $steps
     */
    public function addSteps($steps): TransBase
    {
        $this->steps[] = $steps;
        return $this;
    }

    public function getPayloads(): array
    {
        return $this->payloads;
    }

    /**
     * @param string[] $payloads
     */
    public function setPayloads(array $payloads): TransBase
    {
        $this->payloads = $payloads;
        return $this;
    }

    /**
     * @param mixed $payloads
     */
    public function addPayloads($payloads): TransBase
    {
        $this->payloads[] = $payloads;
        return $this;
    }

    public function getBinPayloads(): array
    {
        return $this->binPayloads;
    }

    public function setBinPayloads(array $binPayloads): TransBase
    {
        $this->binPayloads = $binPayloads;
        return $this;
    }

    /**
     * @param mixed $binPayloads
     */
    public function addBinPayloads($binPayloads): TransBase
    {
        $this->binPayloads[] = $binPayloads;
        return $this;
    }

    public function getIdGenerator(): IdGeneratorInterface
    {
        return $this->idGenerator;
    }

    public function setIdGenerator(IdGeneratorInterface $idGenerator): TransBase
    {
        $this->idGenerator = $idGenerator;
        return $this;
    }

    public function getOp(): string
    {
        return $this->op;
    }

    public function setOp(string $op): TransBase
    {
        $this->op = $op;
        return $this;
    }

    public function getQueryPrepared(): array
    {
        return $this->query_prepared;
    }

    public function setQueryPrepared(array $query_prepared): TransBase
    {
        $this->query_prepared = $query_prepared;
        return $this;
    }

    /**
     * @param mixed $query_prepared
     */
    public function addQueryPrepared($query_prepared): TransBase
    {
        $this->query_prepared[] = $query_prepared;
        return $this;
    }
}
