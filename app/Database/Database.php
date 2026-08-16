<?php

declare(strict_types=1);

namespace SchoolERP\Database;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;
use SchoolERP\Config\Config;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Database
 * --------------------------------------------------------------------------
 *
 * PDO database connection manager.
 *
 * Responsibilities:
 *
 * - Create and maintain a PDO connection.
 * - Configure PDO.
 * - Execute parameterized SQL statements.
 * - Provide SELECT, INSERT, UPDATE and DELETE helpers.
 * - Provide transaction support.
 */
final class Database
{
    /**
     * Framework configuration.
     */
    private Config $config;

    /**
     * PDO connection.
     */
    private ?PDO $connection = null;

    /**
     * Create the database manager.
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Get the PDO connection.
     */
    public function connection(): PDO
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        return $this->connect();
    }

    /**
     * Create the PDO connection.
     */
    private function connect(): PDO
    {
        $driver = $this->config->get(
            'database.default'
        );

        if (!is_string($driver) || $driver === '') {
            throw new RuntimeException(
                'Default database driver is not configured.'
            );
        }

        $config = $this->config->get(
            "database.{$driver}"
        );

        if (!is_array($config)) {
            throw new RuntimeException(
                'Database configuration not found.'
            );
        }

        $host = (string) (
            $config['host'] ?? '127.0.0.1'
        );

        $port = (int) (
            $config['port'] ?? 3306
        );

        $database = (string) (
            $config['database'] ?? ''
        );

        $charset = (string) (
            $config['charset'] ?? 'utf8mb4'
        );

        $username = (string) (
            $config['username'] ?? ''
        );

        $password = (string) (
            $config['password'] ?? ''
        );

        if ($database === '') {
            throw new RuntimeException(
                'Database name is not configured.'
            );
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $host,
            $port,
            $database,
            $charset
        );

        try {
            $this->connection = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE =>
                        PDO::ERRMODE_EXCEPTION,

                    PDO::ATTR_DEFAULT_FETCH_MODE =>
                        PDO::FETCH_ASSOC,

                    PDO::ATTR_EMULATE_PREPARES =>
                        false,

                    PDO::ATTR_STRINGIFY_FETCHES =>
                        false,
                ]
            );

            return $this->connection;
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Unable to connect to the database.',
                0,
                $exception
            );
        }
    }

    /**
     * Prepare and execute a SQL statement.
     *
     * @param array<string,mixed> $bindings
     */
    private function statement(
        string $sql,
        array $bindings = []
    ): PDOStatement {
        $statement = $this->connection()
            ->prepare($sql);

        $statement->execute(
            $bindings
        );

        return $statement;
    }

    /**
     * Execute a SELECT query.
     *
     * @param array<string,mixed> $bindings
     *
     * @return array<int,array<string,mixed>>
     */
    public function select(
        string $sql,
        array $bindings = []
    ): array {
        return $this->statement(
            $sql,
            $bindings
        )->fetchAll();
    }

    /**
     * Execute a SELECT query and return one row.
     *
     * @param array<string,mixed> $bindings
     *
     * @return array<string,mixed>|null
     */
    public function selectOne(
        string $sql,
        array $bindings = []
    ): ?array {
        $result = $this->statement(
            $sql,
            $bindings
        )->fetch();

        if ($result === false) {
            return null;
        }

        return $result;
    }

    /**
     * Execute an INSERT statement.
     *
     * Returns true when at least one row was inserted.
     *
     * @param array<string,mixed> $bindings
     */
    public function insert(
        string $sql,
        array $bindings = []
    ): bool {
        return $this->statement(
            $sql,
            $bindings
        )->rowCount() > 0;
    }

    /**
     * Execute an UPDATE statement.
     *
     * Returns the number of affected rows.
     *
     * @param array<string,mixed> $bindings
     */
    public function update(
        string $sql,
        array $bindings = []
    ): int {
        return $this->statement(
            $sql,
            $bindings
        )->rowCount();
    }

    /**
     * Execute a DELETE statement.
     *
     * Returns the number of deleted rows.
     *
     * @param array<string,mixed> $bindings
     */
    public function delete(
        string $sql,
        array $bindings = []
    ): int {
        return $this->statement(
            $sql,
            $bindings
        )->rowCount();
    }

    /**
     * Get the ID generated by the last INSERT.
     */
    public function lastInsertId(): string
    {
        return $this->connection()
            ->lastInsertId();
    }

    /**
     * Begin a database transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->connection()
            ->beginTransaction();
    }

    /**
     * Commit the current transaction.
     */
    public function commit(): bool
    {
        return $this->connection()
            ->commit();
    }

    /**
     * Roll back the current transaction.
     */
    public function rollBack(): bool
    {
        return $this->connection()
            ->rollBack();
    }

    /**
     * Determine whether a transaction is active.
     */
    public function inTransaction(): bool
    {
        return $this->connection()
            ->inTransaction();
    }

    /**
     * Execute a callback inside a database transaction.
     *
     * The transaction is committed when the callback succeeds.
     *
     * If the callback throws an exception, the transaction is rolled back
     * and the exception is re-thrown.
     *
     * @template T
     *
     * @param callable():T $callback
     *
     * @return T
     */
    public function transaction(
        callable $callback
    ): mixed {
        $this->beginTransaction();

        try {
            $result = $callback();

            $this->commit();

            return $result;
        } catch (\Throwable $exception) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }

            throw $exception;
        }
    }
}