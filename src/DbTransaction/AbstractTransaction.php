<?php

namespace DtmClient\DbTransaction;

use DtmClient\Exception\RuntimeException;
use Hyperf\Context\Context;
use Hyperf\DbConnection\Db;
use Hyperf\Pool\Exception\ConnectionException;
use Hyperf\Utils\Coroutine;
use PDO;
use PDOStatement;

abstract class AbstractTransaction implements DBTransactionInterface
{
    protected array $config = [
        'host' => 'localhost',
        'port' => 3306,
        'database' => '',
        'username' => '',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'fetch_mode' => PDO::FETCH_ASSOC,
        'options' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_AUTOCOMMIT => false,
        ],
    ];

    protected function connect(array $config = []): PDO
    {
        if (! isset($config['options'][PDO::ATTR_AUTOCOMMIT]) && ! isset($this->config['options'][PDO::ATTR_AUTOCOMMIT])) {
            throw new RuntimeException('plase set autocommit is false');
        }

        $pdo = Context::get('dtm.connect');
        if (! empty($pdo)) {
            return $pdo;
        }
        $this->config = array_replace_recursive($this->config, $config);

        $username = $this->config['username'];
        $password = $this->config['password'];
        $dsn = $this->buildDsn($this->config);
        try {
            $pdo = new \PDO($dsn, $username, $password, $this->config['options']);
        } catch (\Throwable $e) {
            throw new ConnectionException('Connection reconnect failed.:' . $e->getMessage());
        }

        $this->configureCharset($pdo, $this->config);

        $this->configureTimezone($pdo, $this->config);

        Context::set('dtm.connect', $pdo);
        return $pdo;
    }

    /**
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php
     */
    public function xaExecute(string $sql, array $bindings = []): int
    {
        $statement = $this->connect()->prepare($sql);

        $this->bindValues($statement, $bindings);

        $statement->execute();

        return $statement->rowCount();
    }

    /**
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php
     */
    public function xaQuery(string $sql, array $bindings = []): bool|array
    {
        // For select statements, we'll simply execute the query and return an array
        // of the database result set. Each element in the array will be a single
        // row from the database table, and will either be an array or objects.
        $statement = $this->connect()->prepare($sql);

        $this->bindValues($statement, $bindings);

        $statement->execute();

        $fetchMode = $this->config['fetch_mode'];

        return $statement->fetchAll($fetchMode);
    }
    
    public function xaExec(string $sql, array $config = []): int|false
    {
        return $this->connect($config)->exec($sql);
    }

    /**
     * Build the DSN string for a host / port configuration.
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php
     */
    protected function buildDsn(array $config): string
    {
        $host = $config['host'] ?? null;
        $port = $config['port'] ?? 3306;
        $database = $config['database'] ?? null;
        return sprintf('mysql:host=%s;port=%d;dbname=%s', $host, $port, $database);
    }

    /**
     * Configure the connection character set and collation.
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php
     */
    protected function configureCharset(PDO $connection, array $config)
    {
        if (isset($config['charset'])) {
            $connection->prepare(sprintf("set names '%s'%s", $config['charset'], $this->getCollation($config)))->execute();
        }
    }

    /**
     * Configure the timezone on the connection.
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php
     */
    protected function configureTimezone(PDO $connection, array $config): void
    {
        if (isset($config['timezone'])) {
            $connection->prepare(sprintf('set time_zone="%s"', $config['timezone']))->execute();
        }
    }

    /**
     * Bind values to their parameters in the given statement.
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php
     */
    protected function bindValues(PDOStatement $statement, array $bindings): void
    {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(
                is_string($key) ? $key : $key + 1,
                $value,
                is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }
    }

    /**
     * Get the collation for the configuration.
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php
     */
    protected function getCollation(array $config): string
    {
        return isset($config['collation']) ? " collate '{$config['collation']}'" : '';
    }
}