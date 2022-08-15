<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Api\ApiInterface;
use DtmClient\Api\RequestBranch;
use DtmClient\Constants\Branch;
use DtmClient\Constants\Operation;
use DtmClient\Constants\Protocol;
use DtmClient\Constants\TransType;
use DtmClient\DbTransaction\DBTransactionInterface;
use DtmClient\Exception\InvalidArgumentException;
use DtmClient\Exception\UnsupportedException;
use Google\Protobuf\Internal\Message;
use PDO;

class XA extends AbstractTransaction
{
    protected Barrier $barrier;

    protected BranchIdGeneratorInterface $branchIdGenerator;

    protected DtmImp $dtmimp;

    protected DBTransactionInterface $dbTransaction;

    public function __construct(ApiInterface $api, Barrier $barrier, BranchIdGeneratorInterface $branchIdGenerator, DtmImp $dtmImp, DBTransactionInterface $dbTransaction)
    {
        $this->api = $api;
        $this->barrier = $barrier;
        $this->branchIdGenerator = $branchIdGenerator;
        $this->dtmimp = $dtmImp;
        $this->dbTransaction = $dbTransaction;
    }

    /**
     * start a xa local transaction.
     */
    public function localTransaction(callable $callback): void
    {
        if (TransContext::getOp() == Branch::BranchCommit || TransContext::getOp() == Branch::BranchRollback) {
            $this->dtmimp->xaHandlePhase2(TransContext::getGid(), TransContext::getBranchId(), TransContext::getOp());
            return;
        }

        $this->dtmimp->xaHandleLocalTrans(function () use ($callback) {
            $callback($this->dbTransaction);
            switch ($this->api->getProtocol()) {
                case Protocol::GRPC:
                    $body = [
                        'BranchID' => TransContext::getBranchId(),
                        'Gid' => TransContext::getGid(),
                        'TransType' => TransType::XA,
                        'Data' => ['url' => TransContext::getPhase2URL()],
                    ];
                    break;
                case Protocol::HTTP:
                case Protocol::JSONRPC_HTTP:
                    $body = [
                        'url' => TransContext::getPhase2URL(),
                        'branch_id' => TransContext::getBranchId(),
                        'gid' => TransContext::getGid(),
                        'trans_type' => TransType::XA,
                    ];
                    break;
                default:
                    throw new UnsupportedException('Unsupported protocol');
            }
            return $this->api->registerBranch($body);
        });
    }

    /**
     * @param null|mixed $rpcReply
     * @throws InvalidArgumentException
     */
    public function callBranch(string $url, array|Message $body, array $rpcReply = null)
    {
        $subBranch = $this->branchIdGenerator->generateSubBranchId();
        switch ($this->api->getProtocol()) {
            case Protocol::HTTP:
            case Protocol::JSONRPC_HTTP:
                $requestBranch = new RequestBranch();
                $requestBranch->body = $body;
                $requestBranch->url = $url;
                $requestBranch->phase2Url = $url;
                $requestBranch->op = Operation::ACTION;
                $requestBranch->method = 'POST';
                $requestBranch->branchId = $subBranch;
                $requestBranch->branchHeaders = TransContext::$branchHeaders;
                return $this->api->transRequestBranch($requestBranch);
            case Protocol::GRPC:
                if (! $body instanceof Message) {
                    throw new InvalidArgumentException('$body must be instance of Message');
                }
                $branchRequest = new RequestBranch();
                $branchRequest->grpcArgument = $body;
                $branchRequest->url = $url;
                $branchRequest->phase2Url = $url;
                $branchRequest->op = Operation::ACTION;
                ! empty($rpcReply) && $branchRequest->grpcDeserialize = $rpcReply;
                $branchRequest->grpcMetadata = [
                    'dtm-gid' => TransContext::getGid(),
                    'dtm-trans_type' => TransType::XA,
                    'dtm-branch_id' => $subBranch,
                    'dtm-op' => Operation::ACTION,
                    'dtm-dtm' => TransContext::getDtm(),
                    'dtm-phase2_url' => $url,
                    'dtm-url' => $url,
                ];
                return $this->api->transRequestBranch($branchRequest);
            default:
                throw new UnsupportedException('Unsupported protocol');
        }
    }

    /**
     * start a xa global transaction.
     * @throws \Throwable
     */
    public function globalTransaction(string $gid, callable $callback)
    {
        var_dump('start globalTransaction');
        $this->init($gid);
        var_dump('prepare start');
        $this->api->prepare(TransContext::toArray());
        var_dump('prepare end');
        try {
            $callback();
            var_dump('start submit');
            $this->api->submit(TransContext::toArray());
            var_dump('end submit');
        } catch (\Throwable $throwable) {
            var_dump('start abort');
            $this->api->abort(TransContext::toArray());
            var_dump('end abort');
            throw $throwable;
        }
        var_dump('end');
    }

    protected function init(?string $gid = null)
    {
        if ($gid === null) {
            $gid = $this->generateGid();
        }
        TransContext::init($gid, TransType::XA, '');
        TransContext::setOp(Operation::ACTION);
    }
}
