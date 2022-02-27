<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\JsonRpc;

use Hyperf\Contract\IdGeneratorInterface;
use Hyperf\LoadBalancer\LoadBalancerManager;
use Hyperf\Rpc\Protocol;
use Hyperf\Rpc\ProtocolManager;
use Hyperf\RpcClient\AbstractServiceClient;
use Hyperf\RpcClient\Client;
use Hyperf\RpcClient\Exception\RequestException;
use Psr\Container\ContainerInterface;

class JsonRpcClient extends AbstractServiceClient
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function initClient()
    {
        $this->loadBalancerManager = $this->container->get(LoadBalancerManager::class);
        $protocol = new Protocol($this->container, $this->container->get(ProtocolManager::class), $this->protocol, $this->getOptions());
        $loadBalancer = $this->createLoadBalancer(...$this->createNodes());
        $transporter = $protocol->getTransporter()->setLoadBalancer($loadBalancer);
        $this->client = make(Client::class)
            ->setPacker($protocol->getPacker())
            ->setTransporter($transporter);
        $this->idGenerator = $this->getIdGenerator();
        $this->pathGenerator = $protocol->getPathGenerator();
        $this->dataFormatter = $protocol->getDataFormatter();
        return $this;
    }

    public function setServiceName(string $serviceName)
    {
        $this->serviceName = $serviceName;
        return $this;
    }

    public function send(string $method, array $params, ?string $id = null)
    {
        if (! $id && $this->idGenerator instanceof IdGeneratorInterface) {
            $id = $this->idGenerator->generate();
        }
        $response = $this->client->send($this->__generateData($method, $params, $id));
        if (is_array($response)) {
            $response = $this->checkRequestIdAndTryAgain($response, $id);

            if (array_key_exists('result', $response) || array_key_exists('error', $response)) {
                return $response;
            }
        }
        throw new RequestException('Invalid response.');
    }
}
