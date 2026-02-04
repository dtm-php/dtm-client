<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Constants\DbType;
use DtmClient\Constants\Protocol;
use DtmClient\Constants\TransType;
use DtmClient\Exception\DtmException;
use DtmClient\Exception\UnsupportedException;
use Hyperf\Contract\ConfigInterface;

class Barrier
{
    protected ConfigInterface $config;

    protected MySqlBarrier $mySqlBarrier;

    protected DbBarrier $dbBarrier;

    public function __construct(ConfigInterface $config, MySqlBarrier $mySqlBarrier, DbBarrier $dbBarrier)
    {
        $this->config = $config;
        $this->mySqlBarrier = $mySqlBarrier;
        $this->dbBarrier = $dbBarrier;
    }

    public function call(callable $businessCall)
    {
        return $this->getBarrier()->call($businessCall);
    }

    public function barrierFrom(string $transType, string $gid, string $branchId, string $op, ?string $phase2Url = null, ?string $dtm = null)
    {
        TransContext::setTransType($transType);
        TransContext::setGid($gid);
        TransContext::setBranchId($branchId);
        TransContext::setOp($op);
        // use in XA
        TransContext::setPhase2URL($phase2Url);
        TransContext::setDtm($dtm);
        if (! TransContext::getTransType() || ! TransContext::getGid() || ! TransContext::getBranchId() || ! TransContext::getOp()) {
            $info = sprintf(
                'transType:%s gid:%s branchId:%s op:%s',
                TransContext::getTransType(),
                TransContext::getGid(),
                TransContext::getBranchId(),
                TransContext::getOp(),
            );
            throw new DtmException(sprintf('Invalid transaction info: %s', $info));
        }

        $protocol = $this->config->get('dtm.protocol');
        if ($protocol === Protocol::GRPC && $transType === TransType::XA && (! TransContext::getPhase2URL() || ! TransContext::getDtm())) {
            $info = sprintf(
                'transType:%s gid:%s branchId:%s op:%s phase2url:%s dtm:%s',
                TransContext::getTransType(),
                TransContext::getGid(),
                TransContext::getBranchId(),
                TransContext::getOp(),
                TransContext::getPhase2URL(),
                TransContext::getDtm(),
            );
            throw new DtmException(sprintf('Invalid transaction info: %s', $info));
        }
    }

    public function queryPrepared(string $transType, string $gid)
    {
        return $this->getBarrier()->queryPrepared($transType, $gid);
    }

    protected function getBarrier(): BarrierInterface
    {
        switch ($this->config->get('dtm.barrier.db.type', DbType::MySQL)) {
            case DbType::MySQL:
                return $this->mySqlBarrier;
            case DbType::DB:
                return $this->dbBarrier;
            default:
                throw new UnsupportedException('Barrier DB type is unsupported.');
        }
    }


}
