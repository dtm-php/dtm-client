<?php

namespace DtmClient;


class BranchBarrier
{

    public string $transType;
    public string $gid;
    public string $branchId;
    public string $op;

    public function isValid(): bool
    {
        return $this->transType && $this->gid && $this->branchId && $this->op;
    }

    public function __toString(): string
    {
        return sprintf('transType:%s gid:%s branchId:%s op:%s', $this->transType, $this->gid, $this->branchId, $this->op);
    }

}