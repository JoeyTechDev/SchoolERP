<?php

declare(strict_types=1);

class Database
{
    private string $host = 'localhost';
    private string $database = 'school_erp';
    private string $username = 'root';
    private string $password = '';

    private ?PDO $connection = null;

    public function connect(): PDO
    {
        if ($this->connection === null) {

            try {

                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=utf8mb4',
                    $this->host,
                    $this->database
                );

                $this->connection = new PDO(
                    $dsn,
                    $this->username,
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );

            } catch (PDOException $e) {

                error_log(
                    '[Database Connection Error] ' . $e->getMessage()
                );

                throw new RuntimeException(
                    'Unable to connect to the database.'
                );

            }
        }

        return $this->connection;
    }
}