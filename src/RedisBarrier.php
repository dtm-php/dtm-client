<?php

namespace DtmClient;

use DtmClient\Constants\Branch;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Redis\Redis;

class RedisBarrier implements BarrierInterface
{
    protected static int $barrierId = 0;
    
    protected static Redis $redis;

    protected static ConfigInterface $config;
    
    public function __construct(Redis $redis, ConfigInterface $config)
    {
        static::$redis = $redis;
        static::$config = $config;
    }

    public static function call(): bool
    {
        static::$barrierId++;
        $originAffectedKey = sprintf('%s-%s-%s-%02d', TransContext::getGid(), TransContext::getBranchId(), $originOp, static::$barrierId);
        $originOp = [
                Branch::BranchCancel => Branch::BranchTry,
                Branch::BranchCompensate => Branch::BranchAction,
            ][TransContext::getOp()] ?? '';
        $currentAffectedKey = sprintf('%s-%s-%s-%02d', TransContext::getGid(), TransContext::getBranchId(), TransContext::getOp(), static::$barrierId);

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
        $result = static::$redis->eval($lua, [$originAffectedKey, $currentAffectedKey, $originOp, TransContext::getOp(),  static::$config->get('dtm.barrier_redis_expire', 7 * 86400)], 2);
        if ($result === 'FAILURE') {
            return false;
        }
        return true;
    }

}