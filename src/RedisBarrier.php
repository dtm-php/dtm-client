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

    public static function call()
    {
        static::$barrierId++;
        $bkey1 = sprintf('%s-%s-%s-%02d', TransContext::getGid(), TransContext::getBranchId(), $originOpTransContext::getOp(), static::$barrierId);
        $originOp = [
                Branch::BranchCancel => Branch::BranchTry,
                Branch::BranchCompensate => Branch::BranchAction,
            ][TransContext::getOp()] ?? '';
        $bkey2 = sprintf('%s-%s-%s-%02d', TransContext::getGid(), TransContext::getBranchId(), , static::$barrierId);

        $lua = <<<'SCRIPT'
        local e1 = redis.call('GET', KEYS[1])
        
        if e1 ~= false then
            return
        end
        
        redis.call('SET', KEYS[2], 'op', 'EX', ARGV[2])
        
        if ARGV[1] ~= '' then
            local e2 = redis.call('GET', KEYS[2])
            if e2 == false then
                redis.call('SET', KEYS[2], 'rollback', 'EX', ARGV[2])
                return
            end
        end
        SCRIPT;
        $result = static::$redis->eval($lua, [$bkey1, $bkey2, $originOp, static::$config->get('dtm.barrier_redis_expire', 7 * 86400)], 2);
        if ($result === 'FAILURE') {
            return false;
        }
        return true;
    }

}