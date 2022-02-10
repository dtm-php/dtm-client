<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Api;

use DtmClient\Constants\Operation;
use DtmClient\Constants\Protocol;
use DtmClient\Constants\RequestMessage;
use DtmClient\Constants\Result;
use DtmClient\Exception\FailureException;
use DtmClient\Exception\GenerateException;
use DtmClient\Exception\OngingException;
use DtmClient\Exception\RequestException;
use DtmClient\TransContext;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Hyperf\Contract\ConfigInterface;

class HttpApi implements ApiInterface
{
    protected Client $client;

    protected ConfigInterface $config;

    public function __construct(Client $client, ConfigInterface $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function getProtocol(): string
    {
        return  Protocol::HTTP;
    }

    public function generateGid(): string
    {
        $url = sprintf('/api/dtmsvr/newGid');
        $response = $this->client->get($url)->getBody()->getContents();
        $responseContent = json_decode($response, true);
        if ($responseContent['dtm_result'] !== 'SUCCESS' || empty($responseContent['gid'])) {
            throw new GenerateException($responseContent['message'] ?? '');
        }
        return $responseContent['gid'];
    }

    public function prepare(array $body)
    {
        return $this->transCallDtm('POST', $body, Operation::PREPARE);
    }

    public function submit(array $body)
    {
        return $this->transCallDtm('POST', $body, Operation::SUBMIT);
    }

    public function abort(array $body)
    {
        return $this->transCallDtm('POST', $body, Operation::ABORT);
    }

    public function registerBranch(array $body)
    {
        return $this->transCallDtm('POST', $body, Operation::REGISTER_BRANCH);
    }

    public function query(array $body)
    {
        return $this->transQuery($body, Operation::QUERY);
    }

    public function queryAll(array $body)
    {
        return $this->transQuery($body, Operation::QUERY_ALL);
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
        $dtm = $this->config->get('dtm.server', '127.0.0.1') . ':' . $this->config->get('dtm.port.http', 36789);
        $response = $this->client->request($method, $url, [
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

        if (Result::isOngoing($response)) {
            throw new OngingException();
        } elseif (Result::isFailure($response)) {
            throw new FailureException();
        } elseif (! Result::isSuccess($response)) {
            throw new RequestException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $response;
    }

    /**
     * @throws \DtmClient\Exception\RequestException
     */
    protected function transCallDtm(string $method, array $body, string $operation, array $query = [])
    {
        try {
            $url = sprintf('/api/dtmsvr/%s', $operation);
            $response = $this->getClient()->request($method, $url, [
                'json' => $body,
                'query' => $query
            ]);
            if (! Result::isSuccess($response)) {
                throw new RequestException($response->getReasonPhrase(), $response->getStatusCode());
            }
        } catch (GuzzleException $exception) {
            throw new RequestException($exception->getMessage(), $exception->getCode(), $exception);
        }
        return $response;
    }

    protected function transQuery(array $query, string $operation)
    {
        try {
            $url = sprintf('/api/dtmsvr/%s', $operation);
            $response = $this->getClient()->get($url, [
                'query' => $query
            ]);
            if (! Result::isSuccess($response)) {
                throw new RequestException($response->getReasonPhrase(), $response->getStatusCode());
            }
        } catch (GuzzleException $exception) {
            throw new RequestException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $response;
    }
}
