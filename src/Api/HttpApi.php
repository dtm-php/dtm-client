<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace DtmPhp\DtmClient\Api;

use DtmPhp\DtmClient\Constants\Operation;
use DtmPhp\DtmClient\Exception\GenerateGidException;
use DtmPhp\DtmClient\Exception\RequestException;
use GuzzleHttp\Client;
use Hyperf\Guzzle\ClientFactory;

class HttpApi implements ApiInterface
{
    protected Client $client;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->client = $clientFactory->create();
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

    protected function transCallDtm(string $dtmServer, array $body, string $operation)
    {
        $url = sprintf('%s/api/dtmsvr/%s', $dtmServer, $operation);
        $response = $this->client->post($url, [
            'json' => $body,
        ]);
        $responseContent = json_decode($response->getBody()->getContents(), true);
        if ($responseContent['dtm_result'] !== 'SUCCESS' || $response->getStatusCode() !== 200) {
            throw new RequestException($responseContent['message'] ?? '');
        }
        return null;
    }
}
