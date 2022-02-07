<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Api;

use DtmClient\Constants\Operation;
use DtmClient\Exception\GenerateGidException;
use DtmClient\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Engine\Http\Client;

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
            throw new GenerateGidException($responseContent['message'] ?? '');
        }
        return $responseContent['gid'];
    }

    public function prepare(array $body)
    {
        return $this->transCallDtm($body, Operation::PREPARE);
    }

    public function submit(array $body)
    {
        return $this->transCallDtm($body, Operation::SUBMIT);
    }

    public function abort(array $body)
    {
        return $this->transCallDtm($body, Operation::ABORT);
    }

    public function registerBranch(array $body)
    {
        return $this->transCallDtm($body, Operation::REGISTER_BRANCH);
    }

    public function query(array $body)
    {
        return $this->transCallDtm($body, Operation::QUERY);
    }

    public function queryAll(array $body)
    {
        return $this->transCallDtm($body, Operation::QUERY_ALL);
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

    /**
     * @throws \DtmClient\Exception\RequestException
     */
    protected function transCallDtm(array $body, string $operation)
    {
        try {
            $url = sprintf('/api/dtmsvr/%s', $operation);
            $response = $this->getClient()->post($url, [
                'json' => $body,
            ]);
            $responseContent = json_decode($response->getBody()->getContents(), true);
            if ($responseContent['dtm_result'] !== 'SUCCESS' || $response->getStatusCode() !== 200) {
                throw new RequestException($responseContent['message'] ?? '');
            }
        } catch (GuzzleException $exception) {
            throw new RequestException($exception->getMessage(), $exception->getCode(), $exception);
        }
        return null;
    }
}
