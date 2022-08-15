<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
use DtmClient\Constants\DbType;
use DtmClient\Constants\Protocol;

return [
    'protocol' => Protocol::HTTP,
    'server' => '127.0.0.1',
    'port' => [
        'http' => 36789,
        'grpc' => 36790,
    ],
    'barrier' => [
        'db' => [
            'type' => DbType::MySQL,
        ],
        'apply' => [],
    ],
    // Configuration is required only if you use the XA transaction mode
    'database' => [
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', 3306),
        'database' => env('DB_DATABASE', 'hyperf'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        'fetch_mode' => PDO::FETCH_ASSOC,
        'options' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_AUTOCOMMIT => 0
        ],
    ],
    'guzzle' => [
        'options' => [],
    ],
];
