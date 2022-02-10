<?php

namespace DtmClient;

use DtmClient\Constants\DbType;
use DtmClient\Exception\DtmException;
use DtmClient\Exception\UnsupportedException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;

class Barrier
{
    protected ConfigInterface $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }


    public function call()
    {
        switch ($this->config->get('dtm.barrier.db.type', DbType::MySQL)) {
            case DbType::MySQL:
                return MySqlBarrier::call();
            default:
                throw new UnsupportedException('Barrier DB type is unsupported.');
        }
    }
    
    public function barrierFrom(string $transType, string $gid, string $branchId, string $op)
    {
        TransContext::setTransType($transType);
        TransContext::setGid($gid);
        TransContext::setBranchId($branchId);
        TransContext::setOp($op);
        if (! TransContext::getTransType() || ! TransContext::getGid() || ! TransContext::getBranchId() || ! TransContext::getOp()) {
            $info = 'transType:' . TransContext::getTransType() . ' gid:' . TransContext::getGid() . ' branchId:' . TransContext::getBranchId() . ' op:' . TransContext::getOp();
            throw new DtmException(sprintf('Invalid transaction info: %s', $info));
        }
    }
    
}