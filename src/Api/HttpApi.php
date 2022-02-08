<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Api;

use DtmClient\Constants\Operation;
use DtmClient\Constants\RequestMessage;
use DtmClient\Exception\GenerateException;
use DtmClient\Exception\RequestException;
use DtmClient\TransContext;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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
        $body = $this->handleOptions($body);
        return $this->transCallDtm('POST', $body, Operation::PREPARE);
    }

    public function submit(array $body)
    {
        $body = $this->handleOptions($body);
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

    protected function handleOptions(array $body): array
    {
        $appendBody = [];
        if (TransContext::isWaitResult() !== null) {
            $appendBody['wait_result'] = TransContext::isWaitResult();
        }
        if (TransContext::getTimeoutToFail() !== null) {
            $appendBody['timeout_to_fail'] = TransContext::getTimeoutToFail();
        }
        if (TransContext::getRetryInterval() !== null) {
            $appendBody['retry_interval'] = TransContext::getRetryInterval();
        }
        if (TransContext::getPassthroughHeaders()) {
            $appendBody['passthrough_headers'] = TransContext::getPassthroughHeaders();
        }
        if (TransContext::getBranchHeaders()) {
            $appendBody['branch_headers'] = TransContext::getBranchHeaders();
        }
        if ($appendBody) {
            $body = array_merge($body, $appendBody);
        }
        return $body;
    }

    public function transRequestBranch(string $method, array $body, string $branchID, string $op, string $url, array $branchHeaders = [])
    {
        $dtm = config('dtm-client.server', '127.0.0.1') . config('dtm-client.port.http', 36789);
        $response = $this->client->request($method, $url, [
            'query' => [
                [
                    'dtm' => $dtm,
                    'gid' => TransContext::getGid(),
                    'branch_id' => $branchID,
                    'trans_type' => TransContext::getTransType(),
                    'op' => $op,
                ],
            ],
            'header' => $branchHeaders,
        ]);

        $responseInfo = $response->getBody()->getContents();
        $responseContent = json_decode($responseInfo, true) ?: [];
        $statusCode = $response->getStatusCode();
        if ($statusCode == 425 || $responseContent['dtm_result'] == RequestMessage::ResultOngoing) {
            throw new RequestException($responseInfo, 425);
        }

        if ($statusCode == 409 || $responseContent['dtm_result'] == RequestMessage::ResultFailure) {
            throw new RequestException($responseInfo, 409);
        }

        if ($statusCode != 200) {
            throw new RequestException($responseInfo);
        }

        return $responseContent;
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
            $statusCode = $response->getStatusCode();
            $responseContent = json_decode($response->getBody()->getContents(), true) ?: [];
            if ($responseContent['dtm_result'] !== 'SUCCESS' || $statusCode !== 200) {
                throw new RequestException($responseContent['message'] ?? '');
            }
        } catch (GuzzleException $exception) {
            throw new RequestException($exception->getMessage(), $exception->getCode(), $exception);
        }
        return null;
    }

    protected function transQuery(array $query, string $operation)
    {
        try {
            $url = sprintf('/api/dtmsvr/%s', $operation);
            $response = $this->getClient()->get($url, [
                'query' => $query
            ]);
            $statusCode = $response->getStatusCode();
            $responseContent = json_decode($response->getBody()->getContents(), true) ?: [];
            if ($statusCode !== 200) {
                throw new RequestException($responseContent['message'] ?? '');
            }
        } catch (GuzzleException $exception) {
            throw new RequestException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $responseContent;
    }
}
