<?php

namespace DtmClient;

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
        

    }
}