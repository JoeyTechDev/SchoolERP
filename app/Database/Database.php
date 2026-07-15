<?php

declare(strict_types=1);

namespace SchoolERP\Database;

use PDO;
use PDOException;
use RuntimeException;
use SchoolERP\Config\Config;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Database
 * --------------------------------------------------------------------------
 *
 * PDO Connection Manager
 *
 * Responsibilities
 * ----------------
 * • Create a PDO connection
 * • Configure PDO options
 * • Expose the PDO instance
 * • Reuse a single connection
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
 * Create a PDO connection.
 */
private function connect(): PDO
{
    $driver = $this->config->get('database.default');

    $config = $this->config->get(
        "database.{$driver}"
    );

    if (!is_array($config)) {
        throw new RuntimeException(
            "Database configuration not found."
        );
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );

    try {

        $this->connection = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
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
 */
private function statement(
    string $sql,
    array $bindings = []
): \PDOStatement {

    $statement = $this->connection()
        ->prepare($sql);

    $statement->execute($bindings);

    return $statement;
}

/**
 * Execute a SELECT query.
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

}