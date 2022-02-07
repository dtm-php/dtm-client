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

    public function generateGid(string $dtmServer): string
    {
        $url = sprintf('%s/api/dtmsvr/newGid', $dtmServer);
        $response = $this->client->get($url)->getBody()->getContents();
        $responseContent = json_decode($response, true);
        if ($responseContent['dtm_result'] !== 'SUCCESS' || empty($responseContent['gid'])) {
            throw new GenerateGidException($responseContent['message'] ?? '');
        }
        return $responseContent['gid'];
    }

    public function prepare(string $dtmServer, array $body)
    {
        return $this->transCallDtm($dtmServer, $body, Operation::PREPARE);
    }

    public function submit(string $dtmServer, array $body)
    {
        return $this->transCallDtm($dtmServer, $body, Operation::SUBMIT);
    }

    public function abort(string $dtmServer, array $body)
    {
        return $this->transCallDtm($dtmServer, $body, Operation::ABORT);
    }

    public function registerBranch(string $dtmServer, array $body)
    {
        return $this->transCallDtm($dtmServer, $body, Operation::REGISTER_BRANCH);
    }

    public function query(string $dtmServer, array $body)
    {
        return $this->transCallDtm($dtmServer, $body, Operation::QUERY);
    }

    public function queryAll(string $dtmServer, array $body)
    {
        return $this->transCallDtm($dtmServer, $body, Operation::QUERY_ALL);
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
    protected function transCallDtm(string $dtmServer, array $body, string $operation)
    {
        try {
            $url = sprintf('%s/api/dtmsvr/%s', $dtmServer, $operation);
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
