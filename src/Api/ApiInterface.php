<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Api;

interface ApiInterface
{
    public function getProtocol(): string;

    public function generateGid(): string;

    public function prepare(array $body);

    public function submit(array $body);

    public function abort(array $body);

    public function registerBranch(array $body);

    public function query(array $body);

    public function queryAll(array $body);

    public function transRequestBranch(RequestBranch $requestBranch);
}
