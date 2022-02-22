<?php

namespace DtmClient\Api;

use DtmClient\Constants\Operation;
use DtmClient\Constants\Protocol;
use DtmClient\Constants\Result;
use DtmClient\Exception\FailureException;
use DtmClient\Exception\GenerateException;
use DtmClient\Exception\OngingException;
use DtmClient\Exception\RequestException;
use DtmClient\Exception\UnsupportedException;
use DtmClient\JsonRpc\DtmPatchGenerator;
use DtmClient\TransContext;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Hyperf\Contract\ConfigInterface;
use Hyperf\JsonRpc\DataFormatter;
use Hyperf\JsonRpc\JsonRpcHttpTransporter;
use Hyperf\JsonRpc\PathGenerator;
use Hyperf\Rpc\ProtocolManager;
use Hyperf\RpcClient\AbstractServiceClient;
use Hyperf\Utils\Packer\JsonPacker;
use Psr\Container\ContainerInterface;

class JsonRpcHttpApi extends AbstractServiceClient implements ApiInterface
{
    protected $serviceName = 'dtmserver';
    
    protected $protocol = 'jsonrpc-http';

    protected ConfigInterface $config;


    public function __construct(ContainerInterface $container, DtmPatchGenerator $patchGenerator)
    {
        parent::__construct($container);

        $this->pathGenerator = $patchGenerator;
        $this->config = $container->get(ConfigInterface::class);
    }

    public function getProtocol(): string
    {
        return Protocol::JSONRPC_HTTP;
    }

    public function generateGid(): string
    {
        $res = $this->__request('NewGid', []);
        return $res['gid'];
    }

    public function prepare(array $body)
    {
        return $this->__request('Prepare', $body);
    }

    public function submit(array $body)
    {
        return $this->__request('Submit', $body);
    }

    public function abort(array $body)
    {
        return $this->__request('Abort', $body);
    }

    public function registerBranch(array $body)
    {
        return $this->__request('RegisterBranch', $body);
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
        $dtm = $this->config->get('dtm.server', '127.0.0.1') . ':' . $this->config->get('dtm.port.http', 36789) . '/api/dtmsvr';
        $response = $this->client->request($requestBranch->method, $requestBranch->url, [
            RequestOptions::QUERY => [
                [
                    'dtm' => $dtm,
                    'gid' => TransContext::getGid(),
                    'branch_id' => $requestBranch->branchId,
                    'trans_type' => TransContext::getTransType(),
                    'op' => $requestBranch->op,
                ],
            ],
            RequestOptions::JSON => $requestBranch->body,
            RequestOptions::HEADERS => $requestBranch->branchHeaders,
        ]);
        
        
        $this->__request($requestBranch->method, [
            RequestOptions::QUERY => [
                [
                    'dtm' => $dtm,
                    'gid' => TransContext::getGid(),
                    'branch_id' => $requestBranch->branchId,
                    'trans_type' => TransContext::getTransType(),
                    'op' => $requestBranch->op,
                ],
            ],
            RequestOptions::BODY => $requestBranch->body,
        ]);

        if (Result::isOngoing($response)) {
            throw new OngingException();
        }
        if (Result::isFailure($response)) {
            throw new FailureException();
        }
        if (! Result::isSuccess($response)) {
            throw new RequestException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $response;
    }
}