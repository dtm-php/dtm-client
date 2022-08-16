<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\DbTransaction;

use DtmClient\Config\DatabaseConfigInterface;
use DtmClient\Exception\RuntimeException;
use Hyperf\Context\Context;
use Hyperf\Pool\Exception\ConnectionException;
use PDO;
use PDOStatement;

abstract class AbstractTransaction implements DBTransactionInterface
{
    protected ?DatabaseConfigInterface $databaseConfig = null;

    /**
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php.
     */
    public function xaExecute(string $sql, array $bindings = []): int
    {
        $statement = $this->connect()->prepare($sql);

        $this->bindValues($statement, $bindings);

        $statement->execute();

        return $statement->rowCount();
    }

    /**
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php.
     */
    public function xaQuery(string $sql, array $bindings = []): bool|array
    {
        // For select statements, we'll simply execute the query and return an array
        // of the database result set. Each element in the array will be a single
        // row from the database table, and will either be an array or objects.
        $statement = $this->connect()->prepare($sql);

        $this->bindValues($statement, $bindings);

        $statement->execute();

        $fetchMode = $this->databaseConfig['fetch_mode'];

        return $statement->fetchAll($fetchMode);
    }

    public function xaExec(string $sql): int|false
    {
        return $this->connect()->exec($sql);
    }

    protected function connect(): PDO
    {
        if (! isset($this->databaseConfig->getOptions()[PDO::ATTR_AUTOCOMMIT])) {
            throw new RuntimeException('plase set autocommit is false');
        }

        $pdo = Context::get('dtm.connect');
        if (! empty($pdo)) {
            return $pdo;
        }

        $username = $this->databaseConfig->getUsername();
        $password = $this->databaseConfig->getPassword();
        $dsn = $this->buildDsn();
        try {
            $pdo = new \PDO($dsn, $username, $password, $this->databaseConfig->getOptions());
        } catch (\Throwable $e) {
            throw new ConnectionException('Connection reconnect failed.:' . $e->getMessage());
        }

        $this->configureCharset($pdo);

        $this->configureTimezone($pdo);

        Context::set('dtm.connect', $pdo);
        return $pdo;
    }

    /**
     * Build the DSN string for a host / port configuration.
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php.
     */
    protected function buildDsn(): string
    {
        $host = $this->databaseConfig->getHost() ?? null;
        $port = $this->databaseConfig->getPort() ?? 3306;
        $database = $this->databaseConfig->getDatabase() ?? null;
        return sprintf('mysql:host=%s;port=%d;dbname=%s', $host, $port, $database);
    }

    /**
     * Configure the connection character set and collation.
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php.
     */
    protected function configureCharset(PDO $connection)
    {
        if (! empty($this->databaseConfig->getCharset())) {
            $connection->prepare(sprintf("set names '%s'%s", $this->databaseConfig->getCharset(), $this->getCollation()))->execute();
        }
    }

    /**
     * Configure the timezone on the connection.
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php.
     */
    protected function configureTimezone(PDO $connection): void
    {
        if (! empty($this->databaseConfig->getTimezone())) {
            $connection->prepare(sprintf('set time_zone="%s"', $this->databaseConfig->getTimezone()))->execute();
        }
    }

    /**
     * Bind values to their parameters in the given statement.
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php.
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
     * Code from https://github.com/hyperf/db/blob/master/src/PDOConnection.php.
     */
    protected function getCollation(): string
    {
        return ! empty($this->databaseConfig->getCollation()) ? " collate '{$this->databaseConfig->getCollation()}'" : '';
    }
}
