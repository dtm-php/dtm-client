<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */

namespace DtmClient;

use DtmClient\Api\ApiInterface;
use DtmClient\Constants\Protocol;
use DtmClient\Constants\TransType;
use DtmClient\Context\Context;
use DtmClient\Exception\UnsupportedException;
use Google\Protobuf\Internal\Message;

class Saga extends AbstractTransaction
{
    protected const CONCURRENT = self::class . '.concurrent';

    protected const ORDERS = self::class . '.orders';

    protected ApiInterface $api;

    public function __construct(ApiInterface $api)
    {
        $this->api = $api;
    }

    public function init(?string $gid = null)
    {
        if ($gid === null) {
            $gid = $this->generateGid();
        }
        Context::set(static::CONCURRENT, false);
        Context::set(static::ORDERS, []);
        TransContext::init($gid, TransType::SAGA, '');
    }

    public function add(string $action, string $compensate, array|object $payload): static
    {
        TransContext::addStep([
            'action' => $action,
            'compensate' => $compensate,
        ]);
        switch ($this->api->getProtocol()) {
            case Protocol::HTTP:
            case Protocol::JSONRPC_HTTP:
                TransContext::addPayload([json_encode($payload)]);
                break;
            case Protocol::GRPC:
                /* @var $payload Message */
                TransContext::addBinPayload([$payload->serializeToString()]);
                break;
            default:
                throw new UnsupportedException('Unsupported protocol');
        }

        return $this;
    }

    public function addBranchOrder(int $branch, array $preBranches): static
    {
        $orders = Context::get(static::ORDERS, []);
        $orders[$branch] = $preBranches;
        Context::set(static::ORDERS, $orders);
        return $this;
    }

    public function enableConcurrent()
    {
        Context::set(static::CONCURRENT, true);
    }

    public function submit()
    {
        $this->addConcurrentContext();
        return $this->api->submit(TransContext::toArray());
    }

    public function addConcurrentContext()
    {
        if (Context::get(static::CONCURRENT, false)) {
            TransContext::setCustomData(json_encode([
                'concurrent' => Context::get(static::CONCURRENT),
                'orders' => Context::get(static::ORDERS) ?: null,
            ]));
        }
    }
}
