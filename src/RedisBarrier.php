<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Constants\Branch;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Redis\Redis;

class RedisBarrier implements BarrierInterface
{
    protected int $barrierId = 0;

    protected Redis $redis;

    protected ConfigInterface $config;

    public function __construct(Redis $redis, ConfigInterface $config)
    {
        $this->redis = $redis;
        $this->config = $config;
    }

    public function call(): bool
    {
        ++$this->barrierId;
        $originAffectedKey = sprintf('%s-%s-%s-%02d', TransContext::getGid(), TransContext::getBranchId(), $originOp, $this->barrierId);
        $originOp = [
            Branch::BranchCancel => Branch::BranchTry,
            Branch::BranchCompensate => Branch::BranchAction,
        ][TransContext::getOp()] ?? '';
        $currentAffectedKey = sprintf('%s-%s-%s-%02d', TransContext::getGid(), TransContext::getBranchId(), TransContext::getOp(), $this->barrierId);

        $lua = <<<'SCRIPT'
        
        local e1 = redis.call('SET', KEYS[1], 'op', 'EX', ARGV[2])
        
        if e1 == false then
            return
        end
        
        if el == 'cancel' and el == 'compensate'
            return
        end
        
        
        local e2 = redis.call('SET', KEYS[2], 'op', 'EX', ARGV[2])
        
        if e2 ~= false
            return
        end
        return 'FAILURE'
        SCRIPT;
        $result = $this->redis->eval($lua, [$originAffectedKey, $currentAffectedKey, $originOp, TransContext::getOp(),  $this->config->get('dtm.barrier.redis.expire_seconds', 7 * 86400)], 2);
        if ($result === 'FAILURE') {
            return false;
        }
        return true;
    }
}
