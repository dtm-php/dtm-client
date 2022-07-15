<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Api;

use DtmClient\Constants\Protocol;
use DtmClient\Constants\Result;
use DtmClient\Constants\TransType;
use DtmClient\Exception\FailureException;
use DtmClient\Exception\OngingException;
use DtmClient\Exception\RequestException;
use DtmClient\Exception\UnsupportedException;
use DtmClient\JsonRpc\DtmPatchGenerator;
use DtmClient\JsonRpc\JsonRpcClientManager;
use DtmClient\TransContext;
use GuzzleHttp\Client;
use Hyperf\Contract\ConfigInterface;
use Hyperf\RpcClient\AbstractServiceClient;
use Psr\Container\ContainerInterface;

class JsonRpcHttpApi extends AbstractServiceClient implements ApiInterface
{
    protected $serviceName = 'dtmserver';

    protected $protocol = 'jsonrpc-http';

    protected ConfigInterface $config;

    protected JsonRpcClientManager $jsonRpcClientManager;

    public function __construct(ContainerInterface $container, DtmPatchGenerator $patchGenerator, JsonRpcClientManager $jsonRpcClientManager)
    {
        parent::__construct($container);

        $this->pathGenerator = $patchGenerator;
        $this->config = $container->get(ConfigInterface::class);
        $this->jsonRpcClientManager = $jsonRpcClientManager;
    }

    public function getProtocol(): string
    {
        return Protocol::JSONRPC_HTTP;
    }

    public function generateGid(): string
    {
        $res = $this->__request('newGid', []);
        return $res['gid'];
    }

    public function prepare(array $body)
    {
        return $this->__request('prepare', $body);
    }

    public function submit(array $body)
    {
        return $this->__request('submit', $body);
    }

    public function abort(array $body)
    {
        return $this->__request('abort', $body);
    }

    public function registerBranch(array $body)
    {
        return $this->__request('registerBranch', $body);
    }

    public function query(array $body)
    {
        throw new UnsupportedException('Unsupported Query operation');
    }

    public function queryAll(array $body)
    {
        throw new UnsupportedException('Unsupported Query operation');
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): static
    {
        $this->client = $client;
        return $this;
    }

    public function transRequestBranch(RequestBranch $requestBranch)
    {
        [$serviceName, $method] = $this->parseServiceNameAndMethod($requestBranch->url);
        $options = [
            'dtm' => $this->getServiceName(),
            'gid' => TransContext::getGid(),
            'branch_id' => $requestBranch->branchId,
            'trans_type' => TransContext::getTransType(),
            'op' => $requestBranch->op,
            'body' => $requestBranch->body,
        ];

        if (TransContext::getTransType() == TransType::XA) {
            $options['phase2_url'] = $requestBranch->phase2Url;
        }

        $response = $this->jsonRpcClientManager->getClient($serviceName)->send($method, $options);

        if (Result::isOngoing($response)) {
            throw new OngingException();
        }

        if (Result::isFailure($response)) {
            throw new FailureException();
        }

        if (! Result::isSuccess($response)) {
            throw new RequestException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $response['result'];
    }

    protected function parseServiceNameAndMethod(string $url): array
    {
        $path = explode('.', $url);
        $serviceName = $path[0];
        array_shift($path);
        $method = implode('.', $path);
        return [$serviceName, $method];
    }
}
