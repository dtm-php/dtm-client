<?php

namespace DtmClient\JsonRpc;

interface JsonRpcClientInterface
{
    public function setServiceName(string $serviceName): static;

    public function initClient(): static;

    public function send(string $method, array $params, ?string $id = null): array;
}