<?php

namespace DtmClient;

use DtmClient\Constants\TransType;
use DtmClient\Exception\FailureException;

class Msg extends AbstractTransaction
{

    public function add(string $action, $payload)
    {
        TransContext::addStep(['action' => $action]);
        TransContext::addPayload(json_encode($payload));
    }

    public function prepare(string $queryPrepared)
    {
        TransContext::setQueryPrepared($queryPrepared);
        return $this->api->prepare(TransContext::toArray());
    }

    public function submit()
    {
        return $this->api->submit(TransContext::toArray());
    }

    public function doAndSubmit(string $queryPrepared, callable $businessCall)
    {
        Barrier::barrierFrom(TransType::MSG, TransContext::getGid(), '00', 'msg');
        $this->prepare($queryPrepared);
        try {
            $result = $businessCall();
            $this->submit();
        } catch (FailureException $failureException) {
            $this->api->abort($body);
        } catch (\Exception $exception) {
            // If busicall return an error other than failure, we will query the result
            $this->api->transRequestBranch('GET', [], TransContext::getBranchId(), TransContext::getOp(), $queryPrepared);
        }
    }
}